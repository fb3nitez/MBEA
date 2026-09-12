<div class="editor-container">
  <div class="editor-shell">
    <div class="toolbar" id="toolbar">
      <button type="button" class="toolbar-btn" data-tiptap-button="undo" title="Undo" aria-label="Undo">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 14 4 9l5-5"/><path d="M4 9h10.5a5.5 5.5 0 0 1 0 11H11"/></svg>
      </button>
      <button type="button" class="toolbar-btn" data-tiptap-button="redo" title="Redo" aria-label="Redo">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 14 5-5-5-5"/><path d="M20 9H9.5a5.5 5.5 0 0 0 0 11H13"/></svg>
      </button>

      <span class="toolbar-divider"></span>

      <button type="button" class="toolbar-btn toolbar-btn--text" data-tiptap-button="h1" title="Heading 1" aria-label="Heading 1">H1</button>
      <button type="button" class="toolbar-btn toolbar-btn--text" data-tiptap-button="h2" title="Heading 2" aria-label="Heading 2">H2</button>
      <button type="button" class="toolbar-btn toolbar-btn--text" data-tiptap-button="h3" title="Heading 3" aria-label="Heading 3">H3</button>

      <span class="toolbar-divider"></span>

      <button type="button" class="toolbar-btn" data-tiptap-button="bold" title="Bold" aria-label="Bold">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 12a4 4 0 0 0 0-8H6v8"/><path d="M15 20a4 4 0 0 0 0-8H6v8Z"/></svg>
      </button>
      <button type="button" class="toolbar-btn" data-tiptap-button="italic" title="Italic" aria-label="Italic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="4" x2="10" y2="4"/><line x1="14" y1="20" x2="5" y2="20"/><line x1="15" y1="4" x2="9" y2="20"/></svg>
      </button>
      <button type="button" class="toolbar-btn" data-tiptap-button="strike" title="Strikethrough" aria-label="Strikethrough">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4H9a3 3 0 0 0-2.83 4"/><path d="M14 12a4 4 0 0 1 0 8H6"/><line x1="4" y1="12" x2="20" y2="12"/></svg>
      </button>
      <button type="button" class="toolbar-btn" data-tiptap-button="code" title="Code" aria-label="Inline code">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
      </button>

      <span class="toolbar-divider"></span>

      <button type="button" class="toolbar-btn" data-tiptap-button="bulletList" title="Bullet list" aria-label="Bullet list">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
      </button>
      <button type="button" class="toolbar-btn" data-tiptap-button="orderedList" title="Numbered list" aria-label="Numbered list">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="10" y1="6" x2="21" y2="6"/><line x1="10" y1="12" x2="21" y2="12"/><line x1="10" y1="18" x2="21" y2="18"/><path d="M4 6h1v4"/><path d="M4 10h2"/><path d="M6 18H4c0-1 2-2 2-3s-1-1.5-2-1"/></svg>
      </button>

      <span class="toolbar-divider"></span>

      <button type="button" class="toolbar-btn" data-tiptap-button="blockquote" title="Blockquote" aria-label="Blockquote">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2H4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V21z"/><path d="M15 21c3 0 7-1 7-8V5c0-1.25-.757-2.017-2-2h-4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2h.75c0 2.25.25 4-2.75 4v4z"/></svg>
      </button>
      <button type="button" class="toolbar-btn" data-tiptap-button="codeBlock" title="Code block" aria-label="Code block">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="m10 10-2 2 2 2"/><path d="m14 14 2-2-2-2"/></svg>
      </button>

      <span class="toolbar-divider"></span>

      <button id="saveClinicalNote" type="button" class="toolbar-btn relative" title="Save Note" aria-label="Save Note">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256"><rect width="256" height="256" fill="none"/><path d="M216,83.31V208a8,8,0,0,1-8,8H48a8,8,0,0,1-8-8V48a8,8,0,0,1,8-8H172.69a8,8,0,0,1,5.65,2.34l35.32,35.32A8,8,0,0,1,216,83.31Z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><path d="M80,216V152a8,8,0,0,1,8-8h80a8,8,0,0,1,8,8v64" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><line x1="152" y1="72" x2="96" y2="72" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/></svg>
        <span id="unSaveIndicator" class="hide">&bull;</span>
      </button>
    </div>
    <div id="editor"></div>
  </div>
</div>
