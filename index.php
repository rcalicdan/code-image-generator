<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>CodeSnap — Code to Image</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/atom-one-dark.min.css"
        id="hljs-theme" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <link rel="stylesheet" href="css/styles.css" />
    <script type="module" src="js/app.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body x-data="codeSnap" class="bg-[#0e0e0e] text-gray-200 min-h-screen flex flex-col lg:flex-row overflow-x-hidden">

    <aside class="w-full lg:w-72 bg-[#161616] border-r border-[#2a2a2a] flex flex-col shrink-0 overflow-y-auto">
        <div class="px-5 py-4 border-b border-[#2a2a2a] flex items-center gap-2">
            <div
                class="w-7 h-7 rounded-lg bg-gradient-to-br from-violet-500 to-pink-500 flex items-center justify-center text-xs font-bold">
                &lt;/&gt;</div>
            <span class="font-semibold text-white tracking-wide">CodeSnap</span>
            <span class="ml-auto text-[10px] text-gray-500 bg-[#222] px-2 py-0.5 rounded-full">v1.0</span>
        </div>

        <div class="panel-section px-5 py-4">
            <label class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-2 block">Language</label>
            <select x-model="lang"
                class="w-full bg-[#222] border border-[#333] rounded-lg px-3 py-2 text-sm text-gray-200 focus:outline-none focus:border-violet-500 cursor-pointer">
                <option value="auto">Auto Detect</option>
                <option value="javascript">JavaScript</option>
                <option value="typescript">TypeScript</option>
                <option value="python">Python</option>
                <option value="html">HTML</option>
                <option value="css">CSS</option>
                <option value="java">Java</option>
                <option value="cpp">C++</option>
                <option value="csharp">C#</option>
                <option value="go">Go</option>
                <option value="rust">Rust</option>
                <option value="php">PHP</option>
                <option value="swift">Swift</option>
                <option value="kotlin">Kotlin</option>
                <option value="sql">SQL</option>
                <option value="bash">Bash</option>
                <option value="json">JSON</option>
                <option value="yaml">YAML</option>
                <option value="markdown">Markdown</option>
            </select>
        </div>

        <div class="panel-section px-5 py-4">
            <label class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-2 block">Code Theme</label>
            <select x-model="theme"
                class="w-full bg-[#222] border border-[#333] rounded-lg px-3 py-2 text-sm text-gray-200 focus:outline-none focus:border-violet-500 cursor-pointer">
                <option value="atom-one-dark">Atom One Dark</option>
                <option value="github-dark">GitHub Dark</option>
                <option value="monokai">Monokai</option>
                <option value="dracula">Dracula</option>
                <option value="tokyo-night-dark">Tokyo Night</option>
                <option value="nord">Nord</option>
                <option value="night-owl">Night Owl</option>
                <option value="one-dark">One Dark</option>
                <option value="agate">Agate</option>
            </select>
        </div>

        <div class="panel-section px-5 py-4">
            <label class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-2 block">Background</label>
            <div class="flex flex-wrap gap-2">
                <template x-for="bg in [1,2,3,4,5,6,7,8,9,10]" :key="bg">
                    <div @click="setBg(`bg-gradient-${bg}`)" :class="bgClass === `bg-gradient-${bg}` ? 'active' : ''"
                        class="bg-swatch" :class="`bg-gradient-${bg}`">
                    </div>
                </template>
                <label :class="customBg !== '' ? 'active' : ''"
                    class="bg-swatch flex items-center justify-center bg-[#2a2a2a] relative overflow-hidden"
                    title="Custom Color">
                    <span class="text-sm">🎨</span>
                    <input type="color" @input="setCustomBg"
                        class="absolute opacity-0 inset-0 cursor-pointer w-full h-full" />
                </label>
            </div>
        </div>

        <div class="panel-section px-5 py-4">
            <label class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-2 block">Window Style</label>
            <div class="flex gap-2">
                <button @click="winStyle = 'macos'"
                    :class="winStyle === 'macos' ? 'bg-[#333] text-white border-[#444]' : 'bg-[#222] text-gray-400 border-[#333]'"
                    class="flex-1 py-1.5 rounded-lg text-xs border hover:border-violet-500 transition">macOS</button>
                <button @click="winStyle = 'windows'"
                    :class="winStyle === 'windows' ? 'bg-[#333] text-white border-[#444]' : 'bg-[#222] text-gray-400 border-[#333]'"
                    class="flex-1 py-1.5 rounded-lg text-xs border hover:border-violet-500 transition">Windows</button>
                <button @click="winStyle = 'none'"
                    :class="winStyle === 'none' ? 'bg-[#333] text-white border-[#444]' : 'bg-[#222] text-gray-400 border-[#333]'"
                    class="flex-1 py-1.5 rounded-lg text-xs border hover:border-violet-500 transition">None</button>
            </div>
        </div>

        <div class="panel-section px-5 py-4">
            <label class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-2 flex justify-between">
                Font Size <span class="text-violet-400" x-text="fontSize + 'px'"></span>
            </label>
            <input type="range" x-model="fontSize" min="11" max="22" class="w-full accent-violet-500 cursor-pointer" />
        </div>

        <div class="panel-section px-5 py-4">
            <label class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-2 flex justify-between">
                Padding <span class="text-violet-400" x-text="padding + 'px'"></span>
            </label>
            <input type="range" x-model="padding" min="16" max="96" step="4"
                class="w-full accent-violet-500 cursor-pointer" />
        </div>

        <div class="panel-section px-5 py-4 space-y-3">
            <label class="text-xs text-gray-400 uppercase tracking-wider font-semibold block">Options</label>

            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-300">Line Numbers</span>
                <button @click="showLines = !showLines" :class="showLines ? 'bg-violet-600' : 'bg-[#333]'"
                    class="w-10 h-5 rounded-full relative transition-colors duration-200 focus:outline-none">
                    <span :class="showLines ? 'translate-x-5 bg-white' : 'bg-gray-500'"
                        class="absolute left-0.5 top-0.5 w-4 h-4 rounded-full transition-transform duration-200"></span>
                </button>
            </div>

            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-300">Watermark</span>
                <button @click="showWatermark = !showWatermark" :class="showWatermark ? 'bg-violet-600' : 'bg-[#333]'"
                    class="w-10 h-5 rounded-full relative transition-colors duration-200 focus:outline-none">
                    <span :class="showWatermark ? 'translate-x-5 bg-white' : 'bg-gray-500'"
                        class="absolute left-0.5 top-0.5 w-4 h-4 rounded-full transition-transform duration-200"></span>
                </button>
            </div>

            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-300">Shadow</span>
                <button @click="showShadow = !showShadow" :class="showShadow ? 'bg-violet-600' : 'bg-[#333]'"
                    class="w-10 h-5 rounded-full relative transition-colors duration-200 focus:outline-none">
                    <span :class="showShadow ? 'translate-x-5 bg-white' : 'bg-gray-500'"
                        class="absolute left-0.5 top-0.5 w-4 h-4 rounded-full transition-transform duration-200"></span>
                </button>
            </div>
        </div>

        <div class="panel-section px-5 py-4">
            <label class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-2 block">Window Title</label>
            <input x-model="winTitle" type="text" placeholder="filename.js"
                class="w-full bg-[#222] border border-[#333] rounded-lg px-3 py-2 text-sm text-gray-200 focus:outline-none focus:border-violet-500" />
        </div>

        <div class="px-5 py-4 mt-auto">
            <button id="export-btn" @click="exportImage()" :disabled="isExporting"
                class="w-full bg-gradient-to-r from-violet-600 to-pink-600 hover:from-violet-500 hover:to-pink-500 text-white font-semibold py-3 rounded-xl transition-all duration-200 flex items-center justify-center gap-2 shadow-lg shadow-violet-900/40 active:scale-95 disabled:opacity-75 disabled:cursor-wait">
                <template x-if="!isExporting">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                </template>
                <template x-if="isExporting">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </template>
                <span x-text="isExporting ? 'Exporting...' : 'Export Image'"></span>
            </button>
            <p class="text-center text-xs text-gray-600 mt-2">Saves as PNG</p>
        </div>
    </aside>

    <main class="flex-1 flex flex-col min-h-screen">
        <div class="bg-[#161616] border-b border-[#2a2a2a] px-6 py-3 flex items-center gap-3 shrink-0">
            <span class="text-xs text-gray-500 font-medium uppercase tracking-wider">Preview</span>
            <div class="flex-1 h-px bg-[#2a2a2a]"></div>
            <span class="text-xs text-gray-600">Paste your code below and it will update live</span>
        </div>

        <div class="flex-1 flex items-center justify-center p-8 bg-[#0e0e0e] overflow-auto">
            <div x-ref="exportArea" class="rounded-2xl" :class="bgClass"
                :style="(customBg !== '' ? `background: ${customBg}; ` : '') + `padding: ${padding}px;`">
                <div id="window-frame" class="window-frame overflow-hidden" style="min-width: 480px; max-width: 820px;"
                    :style="showShadow ? 'box-shadow: 0 25px 60px rgba(0,0,0,0.5), 0 10px 25px rgba(0,0,0,0.3);' : 'box-shadow: none;'">
                    <div x-show="winStyle !== 'none'" id="title-bar"
                        class="bg-[#1e1e1e] px-4 py-3 flex items-center gap-2 border-b border-[#2a2a2a]">
                        <div x-show="winStyle === 'macos'" class="flex gap-1.5 mr-2">
                            <div class="w-3 h-3 rounded-full bg-[#ff5f57]"></div>
                            <div class="w-3 h-3 rounded-full bg-[#febc2e]"></div>
                            <div class="w-3 h-3 rounded-full bg-[#28c840]"></div>
                        </div>
                        <div x-show="winStyle === 'windows'" class="flex gap-1.5 mr-2">
                            <div class="w-2.5 h-2.5 rounded-sm bg-gray-600"></div>
                            <div class="w-2.5 h-2.5 rounded-sm bg-gray-600"></div>
                            <div class="w-2.5 h-2.5 rounded-sm bg-red-500"></div>
                        </div>
                        <span x-text="winTitle" class="text-xs text-gray-400 font-mono mx-auto"></span>
                    </div>

                    <div id="code-area" class="bg-[#1e1e1e] relative">
                        <div class="flex">
                            <div x-show="showLines" x-ref="lineNumbersEl"
                                class="line-numbers-col text-xs text-gray-500 py-4 pl-4 select-none font-mono"
                                :style="`font-size:${fontSize}px; line-height:1.6;`" x-text="lineNumbers"></div>

                            <div class="editor-wrapper flex-1 p-4" style="min-height: 120px;">
                                <pre id="code-highlighted" class="text-sm">
                                    <code x-ref="codeHighlighted" class="hljs" :class="lang !== 'auto' ? `language-${lang}` : ''" :style="`font-size:${fontSize}px; line-height:1.6;`" x-html="highlightedCode"></code>
                                </pre>

                                <textarea id="code-input" x-ref="codeInput" x-model="code"
                                    @keydown.tab.prevent="handleTab" @scroll="syncScroll" @input="resizeTextarea($el)"
                                    spellcheck="false" autocomplete="off" autocorrect="off" autocapitalize="off"
                                    class="p-4" :style="`font-size:${fontSize}px; line-height:1.6;`"
                                    placeholder="Paste or type your code here..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div x-show="showWatermark" id="watermark"
                        class="bg-[#1a1a1a] px-4 py-2 flex items-center justify-end border-t border-[#2a2a2a]">
                        <span class="text-[10px] text-gray-600 font-mono">Made with <span
                                class="text-violet-400">CodeSnap</span></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-[#161616] border-t border-[#2a2a2a] px-6 py-2 text-center">
            <p class="text-xs text-gray-600">Click anywhere on the code preview to edit • Use the panel on the left to
                customize</p>
        </div>
    </main>
</body>

</html>