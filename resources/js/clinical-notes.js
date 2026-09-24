import { Editor } from "@tiptap/core";
import { Placeholder } from "@tiptap/extensions";
import BaseImage from "@tiptap/extension-image";
import StarterKit from "@tiptap/starter-kit";

const Image = BaseImage.extend({
    addAttributes() {
        return {
            ...this.parent?.(),

            width: {
                default: null,
                parseHTML: (element) =>
                    element.getAttribute("width") ||
                    element.style.width ||
                    null,
                renderHTML: (attributes) =>
                    attributes.width
                        ? { style: `width: ${attributes.width}` }
                        : {},
            },

            height: {
                default: null,
                parseHTML: (element) =>
                    element.getAttribute("height") ||
                    element.style.height ||
                    null,
                renderHTML: (attributes) =>
                    attributes.height
                        ? { style: `height: ${attributes.height}` }
                        : {},
            },
        };
    },
});

let currentContent = "";
const unSaveIndicator = document.getElementById('unSaveIndicator');

const editor = new Editor({
    element: document.querySelector("#editor"),
    extensions: [
        StarterKit,
        Image.configure({ allowBase64: false }),
        Placeholder.configure({
            placeholder: "Enter clinical notes...",
        }),
    ],
    onUpdate: ({ editor }) => {
        updateUnsavedIndicator(JSON.stringify(editor.getJSON()));
    },
});

// Wire up toolbar buttons
const saveClinicalNoteBtn = document.getElementById("saveClinicalNote");
const uploadClinicalImageBtn = document.getElementById("uploadClinicalImage");
const clinicalImageInput = document.getElementById("clinicalImageInput");
const clinicalImageWidth = document.getElementById("clinicalImageWidth");
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

    const imageSelected = editor.isActive("image");
    clinicalImageWidth.disabled = !imageSelected;
    clinicalImageWidth.value = imageSelected ? (editor.getAttributes("image").width || "") : "";
}

function updateUnsavedIndicator(content = JSON.stringify(editor.getJSON())) {
    unSaveIndicator.classList.toggle('hide', currentContent === content);
}

async function saveNotes() {
    const content = editor.getJSON();
    const savedContent = JSON.stringify(content);
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
        currentContent = savedContent;
        updateUnsavedIndicator();
    }
}

async function uploadClinicalImage(file) {
    const formData = new FormData();
    formData.append("image", file);

    const response = await fetch(`/psychiatrist/patients/${currentPatientId()}/clinical-images`, {
        method: "POST",
        credentials: "same-origin",
        headers: {
            Accept: "application/json",
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": getCsrf(),
        },
        body: formData,
    });

    if (!response.ok) {
        const error = await response.json().catch(() => ({}));
        throw new Error(error.message || "Failed to upload image");
    }

    const result = await response.json();
    editor.chain().focus().setImage({ src: result.url, alt: result.original_name }).run();
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
    try {
        if (!data) {
            editor.commands.clearContent(true);
            currentContent = JSON.stringify(editor.getJSON());
            updateUnsavedIndicator();
            return;
        }

        let content = data;
        while (typeof content === "string") {
            content = JSON.parse(content);
        }

        if (!content || content.type !== "doc") {
            console.warn("Invalid clinical note content:", content);
            return;
        }

        const { tr } = editor.state;
        const node = editor.schema.nodeFromJSON(content);
        currentContent = JSON.stringify(node.toJSON());

        tr.replaceWith(0, editor.state.doc.content.size, node);
        tr.setMeta("addToHistory", false);
        editor.view.dispatch(tr);

        updateUnsavedIndicator();

    } catch (error) {
        console.error("Failed to load clinical note:", error);
        console.error("Received data:", data);
    }
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
uploadClinicalImageBtn.addEventListener("click", () => clinicalImageInput.click());
clinicalImageWidth.addEventListener("change", () => {
    if (!editor.isActive("image")) return;

    editor.chain().focus().updateAttributes("image", {
        width: clinicalImageWidth.value || null,
    }).run();
});
clinicalImageInput.addEventListener("change", async () => {
    const [file] = clinicalImageInput.files;
    if (!file) return;

    uploadClinicalImageBtn.disabled = true;
    try {
        await uploadClinicalImage(file);
    } catch (error) {
        showToast(error.message);
    } finally {
        clinicalImageInput.value = "";
        uploadClinicalImageBtn.disabled = false;
    }
});
window.displayAsHTML = displayAsHTML;
