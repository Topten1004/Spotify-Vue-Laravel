export default class Loader {
    constructor() {
        this.loadedAssets = {};
    }
    /**
     * Check if the url is absolute
     * @param url
     * @returns
     */
    isAbsoluteUrl(url) {
        if (!url)
            return false;
        // Don't match Windows paths `c:\`
        if (/^[a-z]:\\/.test(url)) {
            return false;
        }
        // Scheme: https://tools.ietf.org/html/rfc3986#section-3.1
        // Absolute URL: https://tools.ietf.org/html/rfc3986#section-4.3
        return /^[a-z][a-z\d+\-.]*:/.test(url);
    }
    /**
     * Load js or css asset and return promise resolved on load event.
     */
    loadAsset(url, params = { type: 'js' }) {
        // script is already loaded, return resolved promise
        if (this.loadedAssets[url] === 'loaded' && !params.force) {
            return new Promise((resolve) => resolve());
            // script has never been loaded before, load it, return promise and resolve on script load event
        }
        else if (!this.loadedAssets[url] || (params.force && this.loadedAssets[url] === 'loaded')) {
            this.loadedAssets[url] = new Promise((resolve, reject) => {
                const finalId = params.id || url.split('/').pop();
                if (params.type === 'css') {
                    this.loadStyleAsset(url, finalId, resolve);
                }
                else {
                    this.loadScriptAsset(url, finalId, resolve);
                }
            });
            return this.loadedAssets[url];
            // script is still loading, return existing promise
        }
        else {
            return this.loadedAssets[url];
        }
    }
    loadStyleAsset(url, id, resolve) {
        const style = document.createElement('link');
        style.rel = 'stylesheet';
        style.id = id || url.split('/').pop();
        style.href = url;
        style.onload = () => {
            this.loadedAssets[url] = 'loaded';
            resolve();
        };
        document.head.appendChild(style);
    }
    loadScriptAsset(url, id, resolve) {
        const s = document.createElement('script');
        s.async = true;
        s.id = id || url.split('/').pop();
        s.src = url;
        s.onload = () => {
            this.loadedAssets[url] = 'loaded';
            resolve();
        };
        document.body.appendChild(s);
    }
}
 