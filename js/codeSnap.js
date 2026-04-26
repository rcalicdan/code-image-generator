export default function codeSnap() {
    return {
        code: `function greet(name) {\n  return \`Hello, \${name}!\`;\n}\n\nconsole.log(greet('World'));`,
        highlightedCode: '',
        lineNumbers: '',
        bgClass: 'bg-gradient-1',
        customBg: '',
        winStyle: 'macos',
        winTitle: 'index.js',
        fontSize: 14,
        padding: 48,
        showLines: false,
        showWatermark: true,
        showShadow: true,
        lang: 'auto',
        theme: 'atom-one-dark',
        isExporting: false,

        init() {
            this.updateHighlight();
            
            this.$watch('code', () => this.updateHighlight());
            this.$watch('lang', () => this.updateHighlight());
            this.$watch('theme', (newTheme) => {
                document.getElementById('hljs-theme').href = `https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/${newTheme}.min.css`;
                setTimeout(() => this.updateHighlight(), 100);
            });

            this.$nextTick(() => {
                if (this.$refs.codeInput) this.resizeTextarea(this.$refs.codeInput);
            });
        },

        updateHighlight() {
            const raw = this.code || '';
            const lang = this.lang === 'auto' ? 'auto' : this.lang;
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

            const lines = raw.split('\n').length;
            this.lineNumbers = Array.from({ length: lines }, (_, i) => i + 1).join('\n');
            
            this.$nextTick(() => {
                if (this.$refs.codeInput) this.resizeTextarea(this.$refs.codeInput);
            });
        },

        handleTab(e) {
            const el = e.target;
            const start = el.selectionStart;
            const end = el.selectionEnd;
            this.code = this.code.substring(0, start) + '  ' + this.code.substring(end);
            
            this.$nextTick(() => {
                el.selectionStart = el.selectionEnd = start + 2;
            });
        },

        syncScroll(e) {
            const el = e.target;
            this.$refs.codeHighlighted.parentElement.scrollTop = el.scrollTop;
            this.$refs.codeHighlighted.parentElement.scrollLeft = el.scrollLeft;
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

        async exportImage() {
            this.isExporting = true;
            const exportArea = this.$refs.exportArea;
            const codeInput = this.$refs.codeInput;

            codeInput.style.opacity = '0';

            try {
                const canvas = await window.html2canvas(exportArea, {
                    backgroundColor: null,
                    scale: 2,
                    useCORS: true,
                    logging: false,
                });

                const link = document.createElement('a');
                link.download = `codesnap-${Date.now()}.png`;
                link.href = canvas.toDataURL('image/png');
                link.click();
            } finally {
                codeInput.style.opacity = '1';
                this.isExporting = false;
            }
        }
    };
}