import { Editor } from "@tiptap/core";
import { Placeholder } from "@tiptap/extensions";
import StarterKit from "@tiptap/starter-kit";

let currentContent = "";
const unSaveIndicator = document.getElementById('unSaveIndicator');

const editor = new Editor({
    element: document.querySelector("#editor"),
    extensions: [
        StarterKit,
        Placeholder.configure({
            placeholder: "Enter clinical notes...",
        }),
    ],
    onUpdate: ({ editor }) => {
        const content = JSON.stringify(editor.getJSON());
        if (currentContent === content) {
            unSaveIndicator.classList.add('hide');
        } else {
            unSaveIndicator.classList.remove('hide');
        }
    },
});

// Wire up toolbar buttons
const saveClinicalNoteBtn = document.getElementById("saveClinicalNote");
const buttons = document.querySelectorAll("[data-tiptap-button]");

buttons.forEach((button) => {
    button.addEventListener("click", () => {
        const command = button.dataset.tiptapButton;

        switch (command) {
            case "undo":
                editor.chain().focus().undo().run();
                break;
            case "redo":
                editor.chain().focus().redo().run();
                break;
            case "bold":
                editor.chain().focus().toggleBold().run();
                break;
            case "italic":
                editor.chain().focus().toggleItalic().run();
                break;
            case "strike":
                editor.chain().focus().toggleStrike().run();
                break;
            case "code":
                editor.chain().focus().toggleCode().run();
                break;
            case "h1":
                editor.chain().focus().toggleHeading({ level: 1 }).run();
                break;
            case "h2":
                editor.chain().focus().toggleHeading({ level: 2 }).run();
                break;
            case "h3":
                editor.chain().focus().toggleHeading({ level: 3 }).run();
                break;
            case "bulletList":
                editor.chain().focus().toggleBulletList().run();
                break;
            case "orderedList":
                editor.chain().focus().toggleOrderedList().run();
                break;
            case "blockquote":
                editor.chain().focus().toggleBlockquote().run();
                break;
            case "codeBlock":
                editor.chain().focus().toggleCodeBlock().run();
                break;
        }

        updateToolbar();
    });
});

// Buttons that toggle a mark/node and should show an "active" state
const activeStateMap = {
    bold: () => editor.isActive("bold"),
    italic: () => editor.isActive("italic"),
    strike: () => editor.isActive("strike"),
    code: () => editor.isActive("code"),
    h1: () => editor.isActive("heading", { level: 1 }),
    h2: () => editor.isActive("heading", { level: 2 }),
    h3: () => editor.isActive("heading", { level: 3 }),
    bulletList: () => editor.isActive("bulletList"),
    orderedList: () => editor.isActive("orderedList"),
    blockquote: () => editor.isActive("blockquote"),
    codeBlock: () => editor.isActive("codeBlock"),
};

// Buttons that should be disabled when the action isn't available
const disabledStateMap = {
    undo: () => !editor.can().undo(),
    redo: () => !editor.can().redo(),
};

function updateToolbar() {
    buttons.forEach((button) => {
        const command = button.dataset.tiptapButton;

        const isActiveCheck = activeStateMap[command];
        if (isActiveCheck) {
            button.classList.toggle("is-active", isActiveCheck());
        }

        const isDisabledCheck = disabledStateMap[command];
        if (isDisabledCheck) {
            button.disabled = isDisabledCheck();
        }
    });
}

async function saveNotes() {
    const content = editor.getJSON();
    const patient_id = currentPatientId();

    const response = await fetch(`/psychiatrist/save-note/${patient_id}`, {
        method: "POST",
        credentials: "same-origin",
        headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": getCsrf(),
        },
        body: JSON.stringify({ content }),
    });

    if (!response.ok) {
        throw new Error("Failed to save notes");
    }

    const result = await response.json();

    showToast(result.message || 'internal server error!');

    if (result.success) {
        unSaveIndicator.classList.add('hide');
    }
}

function getCsrf() {
    let meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute("content") : "";
}

function currentPatientId() {
    let modal = document.getElementById("patient-detail-modal");
    return modal ? modal.getAttribute("data-current-patient") : null;
}

function displayAsHTML(data) {
    const content = JSON.parse(data);

    const { tr } = editor.state;
    const slice = editor.schema.nodeFromJSON(content);
    currentContent = data;

    tr.replaceWith(0, editor.state.doc.content.size, slice);
    tr.setMeta('addToHistory', false);

    editor.view.dispatch(tr);
}

function showToast(msg) {
    var t = document.getElementById("toast");
    if (!t) return;
    t.textContent = msg;
    t.classList.remove("hidden");
    clearTimeout(t._timer);
    t._timer = setTimeout(function () {
        t.classList.add("hidden");
    }, 2800);
}

editor.on("selectionUpdate", updateToolbar);
editor.on("update", updateToolbar);
editor.on("transaction", updateToolbar);

// Set the initial toolbar state (e.g. undo/redo disabled on load)
updateToolbar();
saveClinicalNoteBtn.addEventListener("click", saveNotes);
window.displayAsHTML = displayAsHTML;
