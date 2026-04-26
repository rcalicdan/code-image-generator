export default function codeSnap() {
    const STORAGE_KEY = 'codesnap_state';

    function loadState() {
        try {
            const saved = localStorage.getItem(STORAGE_KEY);
            return saved ? JSON.parse(saved) : {};
        } catch {
            return {};
        }
    }

    function saveState(state) {
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
        } catch { }
    }

    const saved = loadState();

    return {
        code: saved.code ?? `function greet(name) {\n  return \`Hello, \${name}!\`;\n}\n\nconsole.log(greet('World'));`,
        highlightedCode: '',
        lineNumbers: [],
        bgClass: saved.bgClass ?? 'bg-gradient-1',
        customBg: saved.customBg ?? '',
        winStyle: saved.winStyle ?? 'macos',
        winTitle: saved.winTitle ?? 'index.js',
        fontSize: saved.fontSize ?? 14,
        padding: saved.padding ?? 48,
        showLines: saved.showLines ?? false,
        showShadow: saved.showShadow ?? true,
        lang: saved.lang ?? 'auto',
        theme: saved.theme ?? 'atom-one-dark',
        isExporting: false,
        containerWidth: 0,
        containerMinHeight: 0,
        isResizing: false,
        panelOpen: false,

        init() {
            this.updateHighlight();

            if (this.theme !== 'atom-one-dark') {
                document.getElementById('hljs-theme').href =
                    `https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/${this.theme}.min.css`;
            }

            const persist = () => saveState({
                code: this.code,
                bgClass: this.bgClass,
                customBg: this.customBg,
                winStyle: this.winStyle,
                winTitle: this.winTitle,
                fontSize: this.fontSize,
                padding: this.padding,
                showLines: this.showLines,
                showShadow: this.showShadow,
                lang: this.lang,
                theme: this.theme,
            });

            this.$watch('code', () => { this.updateHighlight(); persist(); });
            this.$watch('lang', () => { this.updateHighlight(); persist(); });
            this.$watch('theme', (newTheme) => {
                document.getElementById('hljs-theme').href =
                    `https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/${newTheme}.min.css`;
                setTimeout(() => this.updateHighlight(), 100);
                persist();
            });
            this.$watch('bgClass', persist);
            this.$watch('customBg', persist);
            this.$watch('winStyle', persist);
            this.$watch('winTitle', persist);
            this.$watch('fontSize', persist);
            this.$watch('padding', persist);
            this.$watch('showLines', persist);
            this.$watch('showShadow', persist);

            this.$nextTick(() => {
                if (this.$refs.codeInput) this.resizeTextarea(this.$refs.codeInput);
            });
        },

        updateHighlight() {
            const raw = this.code || '';
            const lang = this.lang;
            let result;
            if (lang === 'auto') {
                result = window.hljs.highlightAuto(raw);
            } else {
                try {
                    result = window.hljs.highlight(raw, { language: lang });
                } catch {
                    result = window.hljs.highlightAuto(raw);
                }
            }
            this.highlightedCode = result.value;
            this.lineNumbers = Array.from({ length: raw.split('\n').length }, (_, i) => i + 1);
            this.$nextTick(() => {
                if (this.$refs.codeInput) this.resizeTextarea(this.$refs.codeInput);
            });
        },

        handleTab(e) {
            const el = e.target;
            const start = el.selectionStart;
            const end = el.selectionEnd;
            this.code = this.code.substring(0, start) + '  ' + this.code.substring(end);
            this.$nextTick(() => { el.selectionStart = el.selectionEnd = start + 2; });
        },

        syncScroll(e) {
            const el = e.target;
            const pre = this.$refs.codeHighlighted;
            if (pre) {
                pre.scrollTop = el.scrollTop;
                pre.scrollLeft = el.scrollLeft;
            }
            if (this.$refs.lineNumbersEl) {
                this.$refs.lineNumbersEl.scrollTop = el.scrollTop;
            }
        },

        resizeTextarea(el) {
            el.style.height = 'auto';
            el.style.height = Math.max(120, el.scrollHeight) + 'px';
        },

        setBg(bgName) {
            this.bgClass = bgName;
            this.customBg = '';
        },

        setCustomBg(e) {
            this.bgClass = '';
            this.customBg = e.target.value;
        },

        swatchClass(bg) {
            return `bg-swatch bg-gradient-${bg} ${this.bgClass === `bg-gradient-${bg}` ? 'active' : ''}`;
        },

        resetState() {
            localStorage.removeItem(STORAGE_KEY);
            this.code = `function greet(name) {\n  return \`Hello, \${name}!\`;\n}\n\nconsole.log(greet('World'));`;
            this.bgClass = 'bg-gradient-1';
            this.customBg = '';
            this.winStyle = 'macos';
            this.winTitle = 'index.js';
            this.fontSize = 14;
            this.padding = 48;
            this.showLines = false;
            this.showShadow = true;
            this.lang = 'auto';
            this.theme = 'atom-one-dark';
            this.containerWidth = 0;
            this.containerMinHeight = 0;
            document.getElementById('hljs-theme').href =
                'https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/atom-one-dark.min.css';
            this.$nextTick(() => {
                if (this.$refs.codeInput) this.resizeTextarea(this.$refs.codeInput);
            });
        },

        initResize(e, direction) {
            e.preventDefault();
            const container = this.$refs.resizeContainer;
            const badge = this.$refs.sizeBadge;
            const startX = e.clientX;
            const startY = e.clientY;
            const startW = container.getBoundingClientRect().width;
            const startH = container.getBoundingClientRect().height;

            if (!this.containerWidth) this.containerWidth = startW;

            container.classList.add('resizing');
            this.isResizing = true;

            const onMove = (ev) => {
                const dx = ev.clientX - startX;
                const dy = ev.clientY - startY;

                if (direction === 'e' || direction === 'se') {
                    this.containerWidth = Math.max(320, startW + dx);
                }
                if (direction === 's' || direction === 'se') {
                    this.containerMinHeight = Math.max(0, startH + dy);
                }

                if (badge) {
                    const h = container.getBoundingClientRect().height;
                    badge.textContent = `${Math.round(this.containerWidth)} × ${Math.round(h)}`;
                }
            };

            const onUp = () => {
                container.classList.remove('resizing');
                this.isResizing = false;
                document.removeEventListener('mousemove', onMove);
                document.removeEventListener('mouseup', onUp);
            };

            document.addEventListener('mousemove', onMove);
            document.addEventListener('mouseup', onUp);
        },

        async exportImage() {
            this.isExporting = true;
            const codeInput = this.$refs.codeInput;
            const exportArea = this.$refs.exportArea;

            codeInput.style.opacity = '0';

            const allEls = exportArea.querySelectorAll('*');
            const savedOverflows = [];
            allEls.forEach(el => {
                savedOverflows.push(el.style.overflow);
                const computed = window.getComputedStyle(el).overflow;
                if (computed === 'hidden' || computed === 'auto' || computed === 'scroll') {
                    el.style.overflow = 'visible';
                }
            });

            await new Promise(r => setTimeout(r, 50));

            const fullWidth = exportArea.scrollWidth;
            const fullHeight = exportArea.scrollHeight;

            try {
                const canvas = await window.html2canvas(exportArea, {
                    backgroundColor: null,
                    scale: 2,
                    useCORS: true,
                    logging: false,
                    width: fullWidth,
                    height: fullHeight,
                    windowWidth: fullWidth,
                    windowHeight: fullHeight,
                    scrollX: 0,
                    scrollY: 0,
                });
                const link = document.createElement('a');
                link.download = `codesnap-${Date.now()}.png`;
                link.href = canvas.toDataURL('image/png');
                link.click();
            } finally {
                codeInput.style.opacity = '1';
                allEls.forEach((el, i) => { el.style.overflow = savedOverflows[i]; });
                this.isExporting = false;
            }
        },
    };
}