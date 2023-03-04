<template>
    <div class="ad_container">
        <div class="ad" v-html="adHtml"></div>
    </div>
</template>

<script>
import Loader from "../../../services/Loader.js";
export default {
    props: ["ad_code"],
    data() {
        return {
            adCode: this.ad_code,
            adHtml: "",
            randomId: ""
        };
    },
    created() {
        this.randomId = this.randomIdGenerator(33);
        this.generateHtml();
        this.loadAdJs().then(() => {
            this.executeJsScript();
        });
    },
    methods: {
        executeJsScript() {
            // find any ad code javascript that needs to be executed
            const pattern = /<script\b[^>]*>([\s\S]*?)<\/script>/g;
            let content;
            this.ads = true;
            while ((content = pattern.exec(this.ad_code))) {
                if (content[1]) {
                    const root =
                        "var d = document.createElement('div'); d.innerHTML = $1; document.getElementById('" +
                        this.randomId +
                        "').appendChild(d.firstChild);";
                    const toEval = content[1].replace(
                        /document.write\((.+?)\);/,
                        root
                    );
                    eval(toEval);
                }
            }
        },
        generateHtml() {
            // strip out all script tags from ad code and leave only html
            const adHtml = this.adCode
                .replace(
                    /<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi,
                    ""
                )
                .trim();

            if (adHtml) {
                this.adHtml = adHtml;
            }
        },
        loadAdJs() {
            const promises = [];
            // load ad code script
            const pattern = /<script.*?src=['"](.*?)['"]/g;
            let match;
            const loader = new Loader();
            while ((match = pattern.exec(this.adCode))) {
                if (match[1]) {
                    promises.push(loader.loadAsset(match[1], { type: "js" }));
                }
            }
            return Promise.all(promises);
        },
        randomIdGenerator(length) {
            var result = [];
            var characters =
                "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
            var charactersLength = characters.length;
            for (var i = 0; i < length; i++) {
                result.push(
                    characters.charAt(
                        Math.floor(Math.random() * charactersLength)
                    )
                );
            }
            return result.join("");
        }
    }
};
</script>
