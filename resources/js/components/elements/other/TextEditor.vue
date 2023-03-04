<template>
    <div>
        <!-- Use the component in the right place of the template -->
        <tiptap-vuetify
            :toolbar-attributes="{ color: $vuetify.theme.themes.dark.primary, dark: true }"
            v-model="contentComp"
            :placeholder="placeholder"
            :extensions="extensions"
        />
    </div>
</template>

<script>
// import the component and the necessary extensions
import {
    TiptapVuetify,
    Heading,
    Bold,
    Italic,
    Strike,
    Underline,
    Code,
    Paragraph,
    BulletList,
    OrderedList,
    ListItem,
    Link,
    Blockquote,
    HardBreak,
    HorizontalRule,
    History
} from "tiptap-vuetify";

import "tiptap-vuetify/dist/main.css";

export default {
    // specify TiptapVuetify component in "components"
    props:['initialContent', 'placeholder'],
    components: { TiptapVuetify },
    data(){
        return {
                    // declare extensions you want to use
            extensions: [
                History,
                Blockquote,
                Link,
                Underline,
                Strike,
                Italic,
                ListItem,
                BulletList,
                OrderedList,
                [
                    Heading,
                    {
                        options: {
                            levels: [1, 2, 3]
                        }
                    }
                ],
                Bold,
                Code,
                HorizontalRule,
                Paragraph,
                HardBreak
            ],
            // starting editor's content
            content: this.initialContent
        }
    }
    ,
    computed: {
        contentComp: {
            set(val) {
                this.content = val;
                this.$emit('content', this.content);
            },
            get() {
                return this.content;
            }
        }
    }
};
</script>
