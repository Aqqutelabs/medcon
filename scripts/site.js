(function () {
    const menuToggle = document.querySelector('[data-menu-toggle]');
    const nav = document.querySelector('[data-nav]');

    if (menuToggle && nav) {
        menuToggle.addEventListener('click', () => {
            const isOpen = nav.classList.toggle('is-open');
            document.body.classList.toggle('nav-open', isOpen);
            menuToggle.setAttribute('aria-expanded', String(isOpen));
});

        nav.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => {
                nav.classList.remove('is-open');
                document.body.classList.remove('nav-open');
                menuToggle.setAttribute('aria-expanded', 'false');
            });
        });
    }

    document.querySelectorAll('[data-nav-dropdown]').forEach((dropdown) => {
        const toggle = dropdown.querySelector('[data-nav-dropdown-toggle]');
        if (!toggle) return;
        const close = () => { dropdown.classList.remove('is-open'); toggle.setAttribute('aria-expanded', 'false'); };
        toggle.addEventListener('click', (event) => {
            event.stopPropagation();
            const open = dropdown.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', String(open));
        });
        dropdown.querySelectorAll('a').forEach((link) => link.addEventListener('click', close));
        document.addEventListener('click', (event) => { if (!dropdown.contains(event.target)) close(); });
        document.addEventListener('keydown', (event) => { if (event.key === 'Escape') { close(); toggle.focus(); } });
    });

    document.querySelectorAll('[data-admissions-slider]').forEach((slider) => {
        const tabs = Array.from(slider.querySelectorAll('[data-slide-tab]'));
        let activeIndex = 0;
        let wheelLocked = false;
        let wheelDelta = 0;
        let wheelDirection = 0;

        const setSlide = (index) => {
            activeIndex = Math.max(0, Math.min(tabs.length - 1, index));
            slider.dataset.activeSlide = String(activeIndex);
            wheelDelta = 0;

            tabs.forEach((tab, tabIndex) => {
                const isActive = tabIndex === activeIndex;
                tab.classList.toggle('active', isActive);
                tab.setAttribute('aria-selected', String(isActive));
            });
            slider.querySelectorAll('[role="tabpanel"]').forEach((panel, panelIndex) => {
                panel.setAttribute('aria-hidden', String(panelIndex !== activeIndex));
            });
        };

        tabs.forEach((tab, index) => {
            tab.addEventListener('click', () => setSlide(index));
            tab.addEventListener('keydown', (event) => {
                if (event.key === 'ArrowRight') {
                    event.preventDefault();
                    setSlide(activeIndex + 1);
                    tabs[activeIndex].focus();
                }
                if (event.key === 'ArrowLeft') {
                    event.preventDefault();
                    setSlide(activeIndex - 1);
                    tabs[activeIndex].focus();
                }
            });
        });

        slider.addEventListener('wheel', (event) => {
            if (wheelLocked) {
                event.preventDefault();
                return;
            }

            const direction = Math.sign(event.deltaY);
            if (direction === 0) {
                return;
            }

            const nextIndex = direction > 0 ? activeIndex + 1 : activeIndex - 1;

            // Release normal page scrolling only when the user moves beyond
            // the first or last slide.
            if (nextIndex < 0 || nextIndex >= tabs.length) {
                wheelDelta = 0;
                wheelDirection = 0;
                return;
            }

            event.preventDefault();

            if (direction !== wheelDirection) {
                wheelDelta = 0;
                wheelDirection = direction;
            }

            wheelDelta += Math.abs(event.deltaY);
            if (wheelDelta < 28) {
                return;
            }

            setSlide(nextIndex);
            wheelLocked = true;
            window.setTimeout(() => {
                wheelLocked = false;
                wheelDirection = 0;
            }, 560);
        }, { passive: false });

        setSlide(0);
    });

    document.querySelectorAll('[data-form]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            event.preventDefault();

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const submit = form.querySelector('button[type="submit"]');
            const status = form.querySelector('.form-status');
            const originalText = submit ? submit.textContent : '';

            if (submit) {
                submit.disabled = true;
                submit.textContent = 'Submitting...';
            }

            window.setTimeout(() => {
                form.reset();
                if (status) {
                    status.textContent = form.dataset.success || 'Thank you. Your enquiry has been received for follow-up.';
                }
                if (submit) {
                    submit.disabled = false;
                    submit.textContent = originalText;
                    const icon = document.createElement('i');
                    icon.className = 'hgi-stroke hgi-arrow-right-02';
                    icon.setAttribute('aria-hidden', 'true');
                    submit.appendChild(icon);
                }
            }, 500);
        });
    });

    document.querySelectorAll('[data-degree-status]').forEach((statusSelect) => {
        const form = statusSelect.closest('form');
        const degreeFields = form ? Array.from(form.querySelectorAll('[data-degree-field]')) : [];

        const updateDegreeFields = () => {
            const isApplicable = statusSelect.value !== '' && statusSelect.value !== 'No';
            degreeFields.forEach((label) => {
                label.classList.toggle('is-disabled', !isApplicable);
                label.querySelectorAll('input, select').forEach((control) => {
                    control.disabled = !isApplicable;
                });
            });
        };

        statusSelect.addEventListener('change', updateDegreeFields);
        updateDegreeFields();
    });

    document.querySelectorAll('.btn').forEach((button) => {
        if (button.querySelector('.hgi-stroke')) {
            return;
        }

        const icon = document.createElement('i');
        icon.className = 'hgi-stroke hgi-arrow-right-02';
        icon.setAttribute('aria-hidden', 'true');
        button.appendChild(icon);
    });

    document.querySelectorAll('.college-location').forEach((location) => {
        if (location.querySelector('.hgi-location-01')) {
            return;
        }

        const marker = location.querySelector('span');
        if (marker) {
            marker.remove();
        }

        const icon = document.createElement('i');
        icon.className = 'hgi-stroke hgi-location-01';
        icon.setAttribute('aria-hidden', 'true');
        location.prepend(icon);
    });

    const sectionLinks = Array.from(document.querySelectorAll('[data-section-link]'));
    const schoolSections = Array.from(document.querySelectorAll('[data-school-section]'));
    if (sectionLinks.length && schoolSections.length) {
        const activateSection = (id) => sectionLinks.forEach((link) => link.classList.toggle('active', link.getAttribute('href') === `#${id}`));
        const observer = new IntersectionObserver((entries) => {
            const visible = entries.filter((entry) => entry.isIntersecting).sort((a, b) => a.boundingClientRect.top - b.boundingClientRect.top);
            if (visible[0]) activateSection(visible[0].target.id);
        }, { rootMargin: '-18% 0px -64% 0px', threshold: [0, .1] });
        schoolSections.forEach((section) => observer.observe(section));
        sectionLinks.forEach((link) => link.addEventListener('click', () => activateSection(link.hash.slice(1))));
        activateSection(schoolSections[0].id);
    }

    document.querySelectorAll('[data-fee-configurator]').forEach((calculator) => {
        const form = calculator.querySelector('[data-fee-form]');
        if (!form) return;
        const usdToNgn = 1390;
        const colleges = {
            tmtcs: {short:'TMTCS',name:'The Manila Times College School of Medicine',logo:'img/school/tmtcs-medicine-logo.png',premed:5611,registration:516,tuition:[4480,4480,3840,3840,1920],living:[1056,1056,1056,1056,528],accommodation:[2592,2592,2592,2592,1296]},
            pltci: {short:'PLTCI',name:'PLTCI College of Medicine',logo:'img/school/pltci-logo.png',premed:6062,registration:480,tuition:[2880,2880,2880,2880,1440],living:[1056,1056,1056,1056,528],accommodation:[2304,2304,2304,2304,1152]}
        };
        const money = (value) => new Intl.NumberFormat('en-US',{style:'currency',currency:'USD',maximumFractionDigits:0}).format(value);
        const naira = (value) => '₦' + new Intl.NumberFormat('en-NG',{maximumFractionDigits:0}).format(value);
        const selected = (name) => form.querySelector(`[name="${name}"]:checked`)?.value || form.elements[name]?.value;
        const activityId = () => window.crypto?.randomUUID?.() || Date.now().toString(36) + Math.random().toString(36).slice(2) + Math.random().toString(36).slice(2);
        const visitId = activityId();
        const track = (event) => {
            const selection = {college: selected('college'), background: selected('background'), package: selected('package')};
            try {
                fetch(new URL('calculator-event.php', window.location.href), {
                    method: 'POST', headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({event, ...selection, id: activityId(), visit: visitId}), keepalive: true
                }).catch(() => {});
            } catch (_) { /* Analytics must not interrupt the calculator. */ }
            try { if (typeof window.gtag === 'function') window.gtag('event', 'fee_calculator_' + event, selection); } catch (_) {}
        };
        let engaged = false;
        const engage = () => { if (!engaged) { engaged = true; track('engaged'); } };
        const sharedConfig=new URLSearchParams(window.location.search);
        ['background','package'].forEach((name)=>{const value=sharedConfig.get(name),control=value?form.querySelector(`[name="${name}"][value="${value}"]`):null;if(control)control.checked=true;});
        if(colleges[sharedConfig.get('college')])form.elements.college.value=sharedConfig.get('college');
        const render = () => {
            const background=selected('background'), college=colleges[form.elements.college.value], packageName=selected('package');
            const hasPremed=background!=='biology-bachelor', service=packageName==='premium'?500:300;
            const tuition=college.tuition.reduce((a,b)=>a+b,0), living=college.living.reduce((a,b)=>a+b,0)+(hasPremed?college.living[0]:0), accommodation=college.accommodation.reduce((a,b)=>a+b,0)+(hasPremed?college.accommodation[0]:0);
            const travelAllowance=2000;
            const total=(hasPremed?college.premed:0)+tuition+living+accommodation+college.registration+service+travelAllowance;
            const initialAcademic=hasPremed?college.premed:college.tuition[0];
            const fourMonthLiving=(college.living[0]+college.accommodation[0])/3;
            const startTotal=initialAcademic+fourMonthLiving+college.registration+service+travelAllowance;
            calculator.querySelector('[data-start-usd]').textContent=money(startTotal);
            calculator.querySelector('[data-start-ngn]').textContent=naira(startTotal*usdToNgn)+' at ₦1,390 / US$1';
            calculator.querySelector('[data-total-usd]').textContent=money(total);
            calculator.querySelector('[data-total-ngn]').textContent=naira(total*usdToNgn)+' at ₦1,390 / US$1';
            calculator.querySelector('[data-duration]').textContent=hasPremed?'5 years, 6 months':'4 years, 6 months';
            calculator.querySelector('[data-college-name]').textContent=college.short;
            calculator.querySelector('[data-entry-route]').textContent=hasPremed?'Pre-Med + MD':'Direct MD entry';
            calculator.querySelector('[data-package-name]').textContent=(packageName==='premium'?'Premium':'Basic')+' · '+money(service);
            calculator.querySelector('[data-premed-note]').innerHTML=hasPremed?'<span aria-hidden="true">i</span><p>Pre-Med is included for this academic background.</p>':'<span aria-hidden="true">✓</span><p>A Biology bachelor’s background removes Pre-Med from this estimate, saving '+money(college.premed)+'. Final entry placement remains subject to college assessment.</p>';
            const years=[]; if(hasPremed)years.push(['Pre-Med / foundation',college.premed,college.living[0]+college.accommodation[0]]); college.tuition.forEach((fee,i)=>years.push([i===4?'Final semester':'MD Year '+(i+1),fee,college.living[i]+college.accommodation[i]]));
            calculator.querySelector('[data-year-rows]').innerHTML=years.map(([stage,fee,other])=>`<tr><td>${stage}</td><td>${money(fee)}</td><td>${money(other)}</td><td><strong>${money(fee+other)}</strong></td></tr>`).join('');
        };
        form.addEventListener('change', () => { render(); engage(); track('change'); });
        const configuredUrl=()=>{const url=new URL(window.location.href);url.searchParams.set('background',selected('background'));url.searchParams.set('college',form.elements.college.value);url.searchParams.set('package',selected('package'));url.hash='fee-configurator';return url.toString();};
        calculator.querySelector('[data-share-estimate]')?.addEventListener('click', async () => {
            engage(); track('share_attempt');
            const url = new URL(configuredUrl()); url.searchParams.set('fee_shared', '1');
            const copy = async () => {
                if (!navigator.clipboard) { window.prompt('Copy this estimate link:', url.toString()); return; }
                await navigator.clipboard.writeText(url.toString());
                track('copy_success'); window.alert('Configured estimate link copied.');
            };
            try {
                if (navigator.share) {
                    try {
                        await navigator.share({title:'My Medcon fee configuration', text:'View my personalised medical-school fee configuration.', url:url.toString()});
                        track('share_success');
                    } catch (error) { if (error.name !== 'AbortError') await copy(); }
                } else await copy();
            } catch (_) { window.prompt('Copy this estimate link:', url.toString()); }
        });
        calculator.querySelector('[data-download-estimate]')?.addEventListener('click',()=>{
            engage(); track('pdf_request');
            document.querySelector('.fee-print-receipt')?.remove();
            const college=colleges[form.elements.college.value], background=form.querySelector('[name="background"]:checked').closest('label').querySelector('strong').textContent;
            const receipt=document.createElement('section');receipt.className='fee-print-receipt';
            receipt.innerHTML=`<header><img src="${college.logo}" alt="${college.name}"><div><span>Personalised fee estimate</span><h1>${college.name}</h1><p>Prepared ${new Intl.DateTimeFormat('en-GB',{dateStyle:'long'}).format(new Date())}</p></div></header><section class="receipt-highlight"><span>To travel now, you need</span><strong>${calculator.querySelector('[data-start-usd]').textContent}</strong><small>${calculator.querySelector('[data-start-ngn]').textContent}</small></section><dl><div><dt>Academic background</dt><dd>${background}</dd></div><div><dt>Entry route</dt><dd>${calculator.querySelector('[data-entry-route]').textContent}</dd></div><div><dt>Medcon support</dt><dd>${calculator.querySelector('[data-package-name]').textContent}</dd></div><div><dt>Complete programme</dt><dd>${calculator.querySelector('[data-total-usd]').textContent}</dd></div></dl><h2>Full breakdown</h2><table><thead><tr><th>Stage</th><th>Tuition</th><th>Living & accommodation</th><th>Total</th></tr></thead><tbody>${calculator.querySelector('[data-year-rows]').innerHTML}</tbody></table><p class="receipt-note">Planning estimate only. Actual invoices may change with exchange rates, school assessment, accommodation, airfare, visa charges and third-party costs.</p><footer><span>Prepared with</span><img src="img/logo.svg" alt="Medcon"><p>Medcon Educational Services and Consultancy Limited</p></footer>`;
            document.body.appendChild(receipt);window.print();window.setTimeout(()=>receipt.remove(),1000);
        });
        render();
        track('view');
        if (sharedConfig.get('fee_shared') === '1') track('shared_visit');
    });
})();
