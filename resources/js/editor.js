import { Editor } from "https://esm.sh/@tiptap/core@2.6.6";
import StarterKit from "https://esm.sh/@tiptap/starter-kit@2.6.6";
import Highlight from "https://esm.sh/@tiptap/extension-highlight@2.6.6";
import Underline from "https://esm.sh/@tiptap/extension-underline@2.6.6";
import Subscript from "https://esm.sh/@tiptap/extension-subscript@2.6.6";
import Superscript from "https://esm.sh/@tiptap/extension-superscript@2.6.6";
import TextStyle from "https://esm.sh/@tiptap/extension-text-style@2.6.6";
import FontFamily from "https://esm.sh/@tiptap/extension-font-family@2.6.6";
import { Color } from "https://esm.sh/@tiptap/extension-color@2.6.6";
import Bold from "https://esm.sh/@tiptap/extension-bold@2.6.6";

// Fungsi untuk membuat editor
function createEditor(elementId, content) {
    const CustomBold = Bold.extend({
        // Override the renderHTML method
        renderHTML({ HTMLAttributes }) {
            return [
                "span",
                {
                    ...HTMLAttributes,
                    style: "font-weight: bold;",
                },
                0,
            ];
        },
        // Ensure it doesn't exclude other marks
        excludes: "",
    });

    // tip tap editor setup
    const editor = new Editor({
        element: document.querySelector(`#${elementId}`),
        extensions: [
            // Exclude the default Bold mark
            StarterKit.configure({
                marks: {
                    bold: false,
                },
            }),
            // Include the custom Bold extension
            CustomBold,
            Highlight,
            Underline,
            Subscript,
            Superscript,
        ],
        content,
        editorProps: {
            attributes: {
                class: "format lg:format-lg dark:format-invert focus:outline-none format-blue max-w-none",
            },
        },
    });

    return editor;
}

// Fungsi untuk menambahkan event listener pada tombol
function addEventListener(editor, buttonId, command) {
    document
        .getElementById(buttonId)
        .addEventListener("click", () =>
            editor.chain().focus()[command]().run()
        );
}

// Fungsi untuk mengatur editor
function setupEditor(elementId, content) {
    const editor = createEditor(elementId, content);

    // set up custom event listeners for the buttons
    addEventListener(
        editor,
        `toggleBoldButton${elementId.replace("wysiwyg-text-example", "")}`,
        "toggleBold"
    );
    addEventListener(
        editor,
        `toggleItalicButton${elementId.replace("wysiwyg-text-example", "")}`,
        "toggleItalic"
    );
    addEventListener(
        editor,
        `toggleUnderlineButton${elementId.replace("wysiwyg-text-example", "")}`,
        "toggleUnderline"
    );
    addEventListener(
        editor,
        `toggleStrikeButton${elementId.replace("wysiwyg-text-example", "")}`,
        "toggleStrike"
    );
    addEventListener(
        editor,
        `toggleSubscriptButton${elementId.replace("wysiwyg-text-example", "")}`,
        "toggleSubscript"
    );
    addEventListener(
        editor,
        `toggleSuperscriptButton${elementId.replace(
            "wysiwyg-text-example",
            ""
        )}`,
        "toggleSuperscript"
    );
}

// Fungsi untuk mengatur editor saat DOMContentLoaded
export default function setupEditors(editors) {
    editors.forEach((editor) => {
        if (document.getElementById(editor.elementId)) {
            setupEditor(editor.elementId, editor.content);
        }
    });
}
