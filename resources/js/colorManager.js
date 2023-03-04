
// any thing related to colors ( format transformation, darking, lighting, updating DOM root varialbles ... )

const defaultThemes = JSON.parse(Settings.find(s => s.key == "themes").value);

// rgb to hex conversion
const rgbToHex = rgb =>
    "#" +
    rgb
        .replace("rgb(", "")
        .replace(")", "")
        .split(",")
        .map(x => {
            const hex = parseInt(x).toString(16);
            return hex.length === 1 ? "0" + hex : hex;
        })
        .join("");

// hex to rgb conversion
const hexToRgb = function(colorHex) {
    var aRgbHex = colorHex.replace("#", "").match(/.{1,2}/g);
    var aRgb =
        "rgb(" +
        parseInt(aRgbHex[0], 16).toString() +
        "," +
        parseInt(aRgbHex[1], 16).toString() +
        "," +
        parseInt(aRgbHex[2], 16).toString() +
        ")";
    return aRgb;
};
// shade rgb color
const RGB_Linear_Shade = (p, c) => {
    var i = parseInt,
        r = Math.round,
        [a, b, c, d] = c.split(","),
        P = p < 0,
        t = P ? 0 : 255 * p,
        P = P ? 1 + p : 1 - p;
    return (
        "rgb" +
        (d ? "a(" : "(") +
        r(i(a[3] == "a" ? a.slice(5) : a.slice(4)) * P + t) +
        "," +
        r(i(b) * P + t) +
        "," +
        r(i(c) * P + t) +
        (d ? "," + d : ")")
    );
};
export default function ColorManager(themes = defaultThemes) {
    // initialize the base color
    this.primaryColor = themes.primary;
    this.secondaryColor = themes.secondary;
    this.darkThemeBgColor = themes.dark.background;
    this.darkThemePanelColor = themes.dark.panel;
    this.darkThemeTextColor = themes.dark.text;
    this.darkThemeTextContMedium = rgbToHex(
        RGB_Linear_Shade(-0.2, hexToRgb(themes.dark.text))
    );
    this.darkThemeTextColorContLow = rgbToHex(
        RGB_Linear_Shade(-0.4, hexToRgb(themes.dark.text))
    );
    this.lightThemeBgColor = themes.light.background;
    this.lightThemePanelColor = themes.light.panel;
    this.lightThemeTextColor = themes.light.text;
    this.lightThemeTextContMedium = rgbToHex(
        RGB_Linear_Shade(-0.4, hexToRgb(themes.light.text))
    );
    this.lightThemeTextColorContLow = rgbToHex(
        RGB_Linear_Shade(-0.1, hexToRgb(themes.light.text))
    );
    // update the root colors
    this.updateRootColors = function() {
        document.documentElement.style.setProperty(
            "--color-primary",
            this.primaryColor
        );
        document.documentElement.style.setProperty(
            "--color-secondary",
            this.secondaryColor
        );

        document.documentElement.style.setProperty(
            "--dark-theme-bg-color",
            this.darkThemeBgColor
        );
        document.documentElement.style.setProperty(
            "--dark-theme-panel-bg-color",
            this.darkThemePanelColor
        );
        document.documentElement.style.setProperty(
            "--dark-theme-text-color",
            this.darkThemeTextColor
        );
        document.documentElement.style.setProperty(
            "--dark-theme-text-color",
            this.darkThemeTextContMedium
        );
        document.documentElement.style.setProperty(
            "--dark-theme-text-color-cont-low",
            this.darkThemeTextColorContLow
        );
        document.documentElement.style.setProperty(
            "--light-theme-bg-color",
            this.lightThemeBgColor
        );
        document.documentElement.style.setProperty(
            "--light-theme-panel-bg-color",
            this.lightThemePanelColor
        );
        document.documentElement.style.setProperty(
            "--light-theme-text-color",
            this.lightThemeTextColor
        );
        document.documentElement.style.setProperty(
            "--light-theme-text-color",
            this.lightThemeTextContMedium
        );
        document.documentElement.style.setProperty(
            "--light-theme-text-color-cont-low",
            this.lightThemeTextColorContLow
        );
    }.bind(this);
}


