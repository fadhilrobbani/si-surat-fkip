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

window.addEventListener("DOMContentLoaded", function () {
    if (document.getElementById("wysiwyg-text-example")) {
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
            element: document.querySelector("#wysiwyg-text-example"),
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
            content:
                "Sehubungan akan dilaksanakan kegiatan Diklat Literasi dan Numerasi bagi Guru Pamong Pendidikan Profesi Guru FKIP Universitas Bengkulu, maka bersama ini kami mohon bantuan Bapak/Ibu mengirimkan narasumber untuk kegiatan tersebut. Kegiatan ini akan dilaksanakan pada:",
            editorProps: {
                attributes: {
                    class: "format lg:format-lg dark:format-invert focus:outline-none format-blue max-w-none",
                },
            },
        });
        // console.log("EDITOR1" + ":  " + editor.getHTML());
        // set up custom event listeners for the buttons
        document
            .getElementById("toggleBoldButton")
            .addEventListener("click", () =>
                editor.chain().focus().toggleBold().run()
            );
        document
            .getElementById("toggleItalicButton")
            .addEventListener("click", () =>
                editor.chain().focus().toggleItalic().run()
            );
        document
            .getElementById("toggleUnderlineButton")
            .addEventListener("click", () =>
                editor.chain().focus().toggleUnderline().run()
            );
        document
            .getElementById("toggleStrikeButton")
            .addEventListener("click", () =>
                editor.chain().focus().toggleStrike().run()
            );
        document
            .getElementById("toggleSubscriptButton")
            .addEventListener("click", () =>
                editor.chain().focus().toggleSubscript().run()
            );
        document
            .getElementById("toggleSuperscriptButton")
            .addEventListener("click", () =>
                editor.chain().focus().toggleSuperscript().run()
            );
    }
});

// editor2
window.addEventListener("DOMContentLoaded", function () {
    if (document.getElementById("wysiwyg-text-example2")) {
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
        const editor2 = new Editor({
            element: document.querySelector("#wysiwyg-text-example2"),
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
            content:
                "Sehubungan akan dilaksanakan kegiatan Diklat Literasi dan Numerasi bagi Guru Pamong Pendidikan Profesi Guru FKIP Universitas Bengkulu, maka bersama ini kami mohon bantuan Bapak/Ibu mengirimkan narasumber untuk kegiatan tersebut. Kegiatan ini akan dilaksanakan pada:",
            editorProps: {
                attributes: {
                    class: "format lg:format-lg dark:format-invert focus:outline-none format-blue max-w-none",
                },
            },
        });
        // console.log("EDITOR2" + ":  " + editor2.getHTML());

        // set up custom event listeners for the buttons
        document
            .getElementById("toggleBoldButton2")
            .addEventListener("click", () =>
                editor2.chain().focus().toggleBold().run()
            );
        document
            .getElementById("toggleItalicButton2")
            .addEventListener("click", () =>
                editor2.chain().focus().toggleItalic().run()
            );
        document
            .getElementById("toggleUnderlineButton2")
            .addEventListener("click", () =>
                editor2.chain().focus().toggleUnderline().run()
            );
        document
            .getElementById("toggleStrikeButton2")
            .addEventListener("click", () =>
                editor2.chain().focus().toggleStrike().run()
            );
        document
            .getElementById("toggleSubscriptButton2")
            .addEventListener("click", () =>
                editor2.chain().focus().toggleSubscript().run()
            );
        document
            .getElementById("toggleSuperscriptButton2")
            .addEventListener("click", () =>
                editor2.chain().focus().toggleSuperscript().run()
            );
    }
});
