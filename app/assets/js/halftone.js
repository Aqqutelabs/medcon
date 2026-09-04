(function () {
    'use strict';

    var DEFAULTS = Object.freeze({
        spacing: 7,
        mobileSpacing: 10,
        minRadius: 0.45,
        maxRadius: 4.1,
        opacity: 0.345,
        imageOpacity: 0.73,
        toneOpacity: 0.27,
        contrast: 1.12,
        brightness: 1,
        backgroundColor: '#16055d',
        imageTone: '#2559ed',
        dotColor: '#dfe8ff',
        accentColor: '#00bba0',
        accentFrequency: 17,
        maxPixelRatio: 1.5
    });

    function coverCrop(sourceWidth, sourceHeight, targetWidth, targetHeight) {
        var scale = Math.max(targetWidth / sourceWidth, targetHeight / sourceHeight);
        var width = sourceWidth * scale;
        var height = sourceHeight * scale;
        return { x: (targetWidth - width) / 2, y: (targetHeight - height) / 2, width: width, height: height };
    }

    function HalftoneImage(root, options) {
        this.root = root;
        this.image = root.querySelector('.mc-halftone-source');
        this.canvas = root.querySelector('.mc-halftone-canvas');
        this.options = Object.assign({}, DEFAULTS, options || {});
        this.frame = 0;
        this.lastSize = '';
        this.observer = null;
        if (!this.image || !this.canvas || !this.canvas.getContext) return;
        this.onReady = this.requestRender.bind(this, true);
        if (this.image.complete && this.image.naturalWidth) this.onReady();
        else this.image.addEventListener('load', this.onReady, { once: true });
        if ('ResizeObserver' in window) {
            this.observer = new ResizeObserver(this.requestRender.bind(this, false));
            this.observer.observe(this.root);
        } else window.addEventListener('resize', this.requestRender.bind(this, false), { passive: true });
    }

    HalftoneImage.prototype.requestRender = function (force) {
        var self = this;
        if (this.frame) cancelAnimationFrame(this.frame);
        this.frame = requestAnimationFrame(function () { self.render(force === true); });
    };

    HalftoneImage.prototype.render = function (force) {
        if (!this.image.naturalWidth) return;
        var rect = this.root.getBoundingClientRect();
        var cssWidth = Math.max(1, Math.round(rect.width));
        var cssHeight = Math.max(1, Math.round(rect.height));
        var sizeKey = cssWidth + 'x' + cssHeight;
        if (!force && sizeKey === this.lastSize) return;
        this.lastSize = sizeKey;

        var ratio = Math.min(window.devicePixelRatio || 1, this.options.maxPixelRatio);
        var width = Math.round(cssWidth * ratio);
        var height = Math.round(cssHeight * ratio);
        var spacing = (cssWidth <= 900 ? this.options.mobileSpacing : this.options.spacing) * ratio;
        var canvas = this.canvas;
        canvas.width = width;
        canvas.height = height;
        var ctx = canvas.getContext('2d', { alpha: false });
        var sample = document.createElement('canvas');
        sample.width = width;
        sample.height = height;
        var sampleCtx = sample.getContext('2d', { willReadFrequently: true });
        var crop = coverCrop(this.image.naturalWidth, this.image.naturalHeight, width, height);

        sampleCtx.fillStyle = this.options.backgroundColor;
        sampleCtx.fillRect(0, 0, width, height);
        sampleCtx.filter = 'grayscale(1) contrast(' + this.options.contrast + ') brightness(' + this.options.brightness + ')';
        sampleCtx.drawImage(this.image, crop.x, crop.y, crop.width, crop.height);
        var pixels = sampleCtx.getImageData(0, 0, width, height).data;

        ctx.fillStyle = this.options.backgroundColor;
        ctx.fillRect(0, 0, width, height);
        ctx.globalAlpha = this.options.imageOpacity;
        ctx.filter = 'saturate(.65) contrast(1.08)';
        ctx.drawImage(this.image, crop.x, crop.y, crop.width, crop.height);
        ctx.filter = 'none';
        ctx.globalCompositeOperation = 'color';
        ctx.globalAlpha = this.options.toneOpacity;
        ctx.fillStyle = this.options.imageTone;
        ctx.fillRect(0, 0, width, height);
        ctx.globalCompositeOperation = 'source-over';
        ctx.globalAlpha = this.options.opacity;

        var minRadius = this.options.minRadius * ratio;
        var maxRadius = this.options.maxRadius * ratio;
        var column = 0;
        for (var y = spacing / 2; y < height; y += spacing) {
            var row = 0;
            for (var x = spacing / 2; x < width; x += spacing) {
                var px = Math.min(width - 1, Math.round(x));
                var py = Math.min(height - 1, Math.round(y));
                var index = (py * width + px) * 4;
                var luminance = (pixels[index] * .2126 + pixels[index + 1] * .7152 + pixels[index + 2] * .0722) / 255;
                var radius = minRadius + luminance * (maxRadius - minRadius);
                if (radius <= minRadius + .05) { row++; continue; }
                ctx.fillStyle = ((row + column * 3) % this.options.accentFrequency === 0 && luminance > .62) ? this.options.accentColor : this.options.dotColor;
                ctx.beginPath();
                ctx.arc(x, y, radius, 0, Math.PI * 2);
                ctx.fill();
                row++;
            }
            column++;
        }
        ctx.globalAlpha = 1;
        this.root.classList.add('is-rendered');
    };

    function init(scope) {
        (scope || document).querySelectorAll('[data-halftone]:not([data-halftone-ready])').forEach(function (root) {
            root.setAttribute('data-halftone-ready', 'true');
            root.halftoneImage = new HalftoneImage(root);
        });
    }

    window.HalftoneImage = HalftoneImage;
    window.applyHalftone = function (root, options) { return new HalftoneImage(root, options); };
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', function () { init(document); });
    else init(document);
    if ('MutationObserver' in window) new MutationObserver(function (records) {
        records.forEach(function (record) { record.addedNodes.forEach(function (node) { if (node.nodeType === 1) init(node.matches && node.matches('[data-halftone]') ? node.parentNode : node); }); });
    }).observe(document.documentElement, { childList: true, subtree: true });
})();
