// Run: node scripts/test-internal-analytics.cjs [path-to-php]
const assert = require('node:assert/strict');
const fs = require('node:fs');
const os = require('node:os');
const path = require('node:path');
const net = require('node:net');
const {spawn, execFileSync} = require('node:child_process');
const vm = require('node:vm');

(async () => {
    const php = process.argv[2] || 'php';
    const root = path.resolve(__dirname, '..');
    const fixture = fs.mkdtempSync(path.join(os.tmpdir(), 'medcon-analytics-'));
    let server;
    try {
        for (const dir of ['includes','data','app/admin','schools/demo']) fs.mkdirSync(path.join(fixture, dir), {recursive:true});
        for (const file of ['site-event.php','includes/site-activity.php']) fs.copyFileSync(path.join(root,file), path.join(fixture,file));
        for (const file of ['index.php','colleges.php','app/admin/dashboard.php','schools/demo/index.php']) fs.writeFileSync(path.join(fixture,file), '<html>Public page fixture</html>');
        fs.writeFileSync(path.join(fixture,'schools/demo/index.php'), "<?php require __DIR__ . '/../../includes/school-page.php';");
        const port = await new Promise(resolve => {const socket=net.createServer();socket.listen(0,'127.0.0.1',()=>{const port=socket.address().port;socket.close(()=>resolve(port));});});
        server = spawn(php, ['-S', `127.0.0.1:${port}`, '-t', fixture], {stdio:'ignore',windowsHide:true});
        let spawnError; server.on('error', error => { spawnError = error; });
        const origin = `http://127.0.0.1:${port}`;
        const endpoint = origin + '/site-event.php';
        let ready = false;
        for (let attempt=0;attempt<50;attempt++) {
            if (spawnError) throw spawnError;
            try { await fetch(endpoint); ready=true;break; } catch (_) { await new Promise(resolve=>setTimeout(resolve,100)); }
        }
        assert(ready,'PHP test server started');
        const headers = {'User-Agent':'Mozilla/5.0 MedconTest','Referer':origin+'/colleges.php','Sec-Fetch-Site':'same-origin'};
        let response = await fetch(endpoint, {headers});
        assert.equal(response.status,200);
        const cookie = response.headers.get('set-cookie').split(';')[0];
        const first = await response.json();
        const post = (access, extra={}, customHeaders={}) => fetch(endpoint, {method:'POST',headers:{...headers,Cookie:cookie,'Content-Type':'application/json',...customHeaders},body:JSON.stringify({token:access.token,kind:'view',...extra})});
        assert.equal((await post(first)).status,204);
        assert.equal((await post(first)).status,204); // same page load replay
        response = await fetch(endpoint,{headers:{...headers,Cookie:cookie,Referer:origin+'/colleges?campaign=ignored'}});
        const second = await response.json();
        assert.equal((await post(second)).status,204); // refresh, same browser
        await Promise.all(Array.from({length:12},(_,index)=>post(second,{kind:'click',button:'Apply now #1',id:'click-test-event-'+index})));
        response = await fetch(endpoint,{headers});
        const otherCookie = response.headers.get('set-cookie').split(';')[0];
        assert.equal((await post(await response.json(),{}, {Cookie:otherCookie})).status,204);
        assert.equal((await post({token:first.token+'tampered'})).status,403);
        assert.equal((await post(first,{}, {Cookie:''})).status,403);
        assert.equal((await post(first,{kind:'invented'})).status,422);
        assert.equal((await fetch(endpoint,{headers:{...headers,'User-Agent':'Googlebot'}})).status,403);
        assert.equal((await fetch(endpoint,{headers:{...headers,'Sec-Fetch-Site':'cross-site'}})).status,403);
        assert.equal((await fetch(endpoint,{headers:{...headers,Referer:origin+'/app/admin/dashboard.php'}})).status,422);
        assert.equal((await fetch(endpoint,{headers:{...headers,Referer:origin+'/nonexistent'}})).status,422);
        const check = `require 'includes/site-activity.php'; $db=site_activity_db(); echo json_encode(['counts'=>$db->query("SELECT COUNT(DISTINCT visitor) visitors, SUM(kind='view') views, SUM(kind='click') clicks, COUNT(DISTINCT page) pages FROM events")->fetch(PDO::FETCH_ASSOC),'root'=>site_activity_page('/index.php'),'school'=>site_activity_page('/schools/demo/index.php')]);`;
        const result = JSON.parse(execFileSync(php,['-r',check],{cwd:fixture,encoding:'utf8'}));
        assert.deepEqual(result.counts,{visitors:2,views:3,clicks:12,pages:1});
        assert.equal(result.root,'/'); assert.equal(result.school,'/schools/demo/');
        console.log('PASS: signed visitor cookies, distinct visitors, refreshes, replay protection, concurrent clicks, URL normalization, bot/private-page rejection and invalid tokens.');

        const listeners = {}; const requests = [];
        const button = {disabled:false,tagName:'BUTTON',textContent:'Apply now',closest:()=>null,hasAttribute:()=>false,getAttribute:()=>null};
        const document = {currentScript:{src:origin+'/scripts/internal-analytics.js'},visibilityState:'hidden',addEventListener:(name,fn)=>listeners[name]=fn,querySelectorAll:()=>[button]};
        const context = {document,window:{},navigator:{},URL,Date,Math,fetch:async(url,options={})=>{requests.push(options);return {ok:true,json:async()=>({token:'test-token'})};}};
        vm.runInNewContext(fs.readFileSync(path.join(root,'scripts/internal-analytics.js'),'utf8'),context);
        assert.equal(requests.length,0,'Hidden pages not counted');
        document.visibilityState='visible'; listeners.visibilitychange();
        await new Promise(resolve=>setImmediate(resolve));
        listeners.visibilitychange();
        listeners.click({isTrusted:false,target:{closest:()=>button}});
        listeners.click({isTrusted:true,target:{closest:()=>button}});
        await new Promise(resolve=>setImmediate(resolve));
        const events=requests.filter(request=>request.body).map(request=>JSON.parse(request.body));
        assert.deepEqual(events.map(event=>event.kind),['view','click']);
        assert.equal(events[1].button,'Apply now #1');
        console.log('PASS: visible-page-only tracking, one view per load, synthetic click exclusion and readable control labels.');
    } finally {
        if (server && server.exitCode === null) { await new Promise(resolve=>{server.once('exit',resolve);server.kill();}); }
        fs.rmSync(fixture,{recursive:true,force:true});
    }
})().catch(error=>{console.error(error);process.exitCode=1;});
