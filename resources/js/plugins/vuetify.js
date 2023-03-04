import Vue from "vue";
import Vuetify from 'vuetify/lib'
import "vuetify/dist/vuetify.min.css";
import ColorManager from "../colorManager";
import { TiptapVuetifyPlugin } from 'tiptap-vuetify';
import { 
    mdiAccount,
    mdiHome,
    mdiChevronDown,
    mdiCompass,
    mdiMusicBoxMultiple,
    mdiMicrophone,
    mdiAccountMusic,
    mdiCog,
    mdiCursorMove,
    mdiPencil,
    mdiDelete,
    mdiChevronLeft,
    mdiChevronRight,
    mdiBell,
    mdiDotsVertical,
    mdiMusicNoteEighth,
    mdiAccountCog,
    mdiBrightness4,
    mdiLogout,
    mdiGoogleAds,
    mdiAlbum,
    mdiPlus,
    mdiMusicNote,
    mdiFolderMusic,
    mdiTextBoxMultiple,
    mdiNotebookMultiple,
    mdiPlaylistMusic,
    mdiPodcast,
    mdiRadioTower,
    mdiHandshakeOutline,
    mdiTranslate,
    mdiCached,
    mdiApplicationCog,
    mdiAirplaneLanding,
    mdiClose,
    mdiNavigationOutline,
    mdiEyeOffOutline,
    mdiEyeOutline,
    mdiPalette,
    mdiGoogleAnalytics,
    mdiAccountKeyOutline,
    mdiCardOutline,
    mdiEmailMultiple,
    mdiTextBoxSearch,
    mdiDatabase,
    mdiArrowUp,
    mdiCheckboxMarkedCircleOutline,
    mdiFacebook,
    mdiAlert,
    mdiCheckboxMarkedCircle,
    mdiStar,
    mdiProfessionalHexagon,
    mdiCheckDecagram,
    mdiStarFourPoints,
    mdiStarCircle,
    mdiLink,
    mdiLinkOff,
    mdiMagnify,
    mdiFeatureSearchOutline,
    mdiHeart,
    mdiPlay,
    mdiYoutube,
    mdiHeartOutline,
    mdiShare,
    mdiFullscreenExit,
    mdiFullscreen,
    mdiRefresh,
    mdiShuffleVariant,
    mdiSkipPrevious,
    mdiRewind10,
    mdiPlayCircle,
    mdiPlayCircleOutline,
    mdiSkipNext,
    mdiFastForward30,
    mdiDownloadCircle,
    mdiClipboardOutline,
    mdiMessageProcessingOutline,
    mdiAccountGroup,
    mdiMenu,
    mdiWhiteBalanceSunny,
    mdiCheck,
    mdiAccessPoint,
    mdiBackupRestore,
    mdiCreditCardRemove,
    mdiDesktopMacDashboard,
    mdiWrench,
    mdiCurrencyUsd,
    mdiTextBoxMultipleOutline,
    mdiAccountSupervisorCircle,
    mdiAccountNetwork,
    mdiAccountCircle,
    mdiApi,
    mdiCamera,
    mdiCrown,
    mdiDotsHorizontal,
    mdiCloudDownloadOutline,
    mdiThumbDown,
    mdiCash,
    mdiAccountTie,
    mdiTagFaces,
    mdiCart,
    mdiPauseCircle,
    mdiFileChart,
    mdiSpotify,
    mdiSoundcloud,
    mdiMusic,
    mdiPlaylistMusicOutline,
    mdiTwitter,
    mdiWhatsapp,
    mdiTelegram,
    mdiInstagram,
    mdiShareVariant,
    mdiMotionPlay,
    mdiVolumeMedium,
    mdiVolumeHigh,
    mdiVolumeOff,
    mdiKey,
    mdiPlaylistPlay,
    mdiCreditCardOutline,
    mdiTextBoxSearchOutline,
    mdiHelp,
    mdiWeatherNight,
    mdiDownload,
    mdiApplicationExport,
    mdiChevronUp,
    mdiWindowClose,
    mdiArrowAll,
    mdiOpenInNew,
    mdiCreditCardRefresh,
    mdiFileFind,
    mdiTicket,
    mdiCashCheck,
    mdiShopping,
    mdiPlaySpeed,
    mdiArrowRight,
    mdiArrowLeft,
    mdiAccountCash,
    mdiMusicCircle,
    mdiAccountCashOutline
} from '@mdi/js'

const colorManager = new ColorManager();
// initializing the base/default colors 
const defaultPrimaryColor = colorManager.primaryColor || '#4245a8';
const defaultSecondaryColor = colorManager.secondaryColor || '#b084ab';
const defaultErrorColor = '#E53935';

const defaultThemes = {
    light: {
        primary : defaultPrimaryColor,
        secondary: defaultSecondaryColor,
        error: defaultErrorColor
    },
        dark: {
        primary : defaultPrimaryColor,
        secondary: defaultSecondaryColor,
        error: defaultErrorColor
    }
} 

const opts = {
    theme: {
        themes: defaultThemes
    },
    breakpoint: {
        thresholds: {
            xs: 500,
            sm: 540,
            md: 800,
            lg: 1280,
        },
        scrollBarWidth: 24,
    },
    icons: {
        iconfont: "mdiSvg", // default - only for display purposes,
        values: {
            'account': mdiAccount,
            'home' : mdiHome,
            'chevron-down' : mdiChevronDown,
            'chevron-up' : mdiChevronUp,
            'compass' : mdiCompass,
            'music-box-multiple' : mdiMusicBoxMultiple,
            'microphone' : mdiMicrophone,
            'account-music' : mdiAccountMusic,
            'cog' : mdiCog,
            'pencil' : mdiPencil,
            'delete' : mdiDelete,
            'chevron-left' : mdiChevronLeft,
            'chevron-right' : mdiChevronRight,
            'bell' : mdiBell,
            'dots-vertical' : mdiDotsVertical,
            'music-note-right' : mdiMusicNoteEighth,
            'account-cog' : mdiAccountCog,
            'brightness-4' : mdiBrightness4,
            'logout' : mdiLogout,
            'google-ads' : mdiGoogleAds,
            'album' : mdiAlbum,
            'plus' : mdiPlus,
            'music-note' : mdiMusicNote,
            'folder-music' : mdiFolderMusic,
            'textBox-multiple' : mdiTextBoxMultiple,
            'notebook-multiple' : mdiNotebookMultiple,
            'playlist-music' : mdiPlaylistMusic,
            'podcast' : mdiPodcast,
            'open-in-new' : mdiOpenInNew,
            'radio-tower' : mdiRadioTower,
            'handshake-outline' : mdiHandshakeOutline,
            'translate' : mdiTranslate,
            'cached' : mdiCached,
            'application-cog' : mdiApplicationCog,
            'airplane-landing' : mdiAirplaneLanding,
            'close' : mdiClose,
            'navigation-outline' : mdiNavigationOutline,
            'eye-off-outline' : mdiEyeOffOutline,
            'eye-outline' : mdiEyeOutline,
            'palette' : mdiPalette,
            'google-analytics' : mdiGoogleAnalytics,
            'account-key-outline' : mdiAccountKeyOutline,
            'card-outline' : mdiCardOutline,
            'email-multiple' : mdiEmailMultiple,
            'text-box-search' : mdiTextBoxSearch,
            'database' : mdiDatabase,
            'arrow-up' : mdiArrowUp,
            'checkbox-marked-circle-outline' : mdiCheckboxMarkedCircleOutline,
            'facebook' : mdiFacebook,
            'alert' : mdiAlert,
            'checkbox-marked-circle' : mdiCheckboxMarkedCircle,
            'star' : mdiStar,
            'professional-hexagon' : mdiProfessionalHexagon,
            'check-decagram' : mdiCheckDecagram,
            'star-four-points' : mdiStarFourPoints,
            'star-circle' : mdiStarCircle,
            'link' : mdiLink,
            'link-off' : mdiLinkOff,
            'magnify' : mdiMagnify,
            'feature-search-outline' : mdiFeatureSearchOutline,
            'heart' : mdiHeart,
            'play' : mdiPlay,
            'youtube' : mdiYoutube,
            'soundcloud' : mdiSoundcloud,
            'heart-outline' : mdiHeartOutline,
            'share' : mdiShare,
            'fullscreen-exit' : mdiFullscreenExit,
            'fullscreen' : mdiFullscreen,
            'refresh' : mdiRefresh,
            'shuffle-variant' : mdiShuffleVariant,
            'skip-previous' : mdiSkipPrevious,
            'rewind-10' : mdiRewind10,
            'play-circle' : mdiPlayCircle,
            'pause-circle' : mdiPauseCircle,
            'play-circle-outline' : mdiPlayCircleOutline,
            'skip-next' : mdiSkipNext,
            'fast-forward-30' : mdiFastForward30,
            'download-circle' : mdiDownloadCircle,
            'clipboard-outline' : mdiClipboardOutline,
            'message-processing-outline' : mdiMessageProcessingOutline,
            'account-group' : mdiAccountGroup,
            'menu' : mdiMenu,
            'white-balance-sunny' : mdiWhiteBalanceSunny,
            'weather-night' : mdiWeatherNight,
            'check' : mdiCheck,
            'access-point' : mdiAccessPoint,
            'arrow-left' : mdiArrowLeft,
            'arrow-right' : mdiArrowRight,
            'backup-restore' : mdiBackupRestore,
            'credi-card-remove' : mdiCreditCardRemove,
            'desktop-mac-dashboard' : mdiDesktopMacDashboard,
            'wrench' : mdiWrench,
            'currency-usd' : mdiCurrencyUsd,
            'text-box-multiple-outline' : mdiTextBoxMultipleOutline,
            'account-supervisor-circle' : mdiAccountSupervisorCircle,
            'account-network' : mdiAccountNetwork,
            'account-circle' : mdiAccountCircle,
            'cursor-move': mdiCursorMove,
            'api': mdiApi,
            'camera': mdiCamera,
            'crown': mdiCrown,
            'dots-horizontal': mdiDotsHorizontal,
            'cloud-download-outline': mdiCloudDownloadOutline,
            'thumb-down': mdiThumbDown,
            'cash': mdiCash,
            'account-tie': mdiAccountTie,
            'account-circle': mdiAccountCircle,
            'tag-faces': mdiTagFaces,
            'cart': mdiCart,
            'file-chart': mdiFileChart,
            'spotify': mdiSpotify,
            'music': mdiMusic,
            'playlist-music-outline': mdiPlaylistMusicOutline,
            'playlist-play': mdiPlaylistPlay,
            'twitter': mdiTwitter,
            'facebook': mdiFacebook,
            'whatsapp': mdiWhatsapp,
            'telegram': mdiTelegram,
            'instagram': mdiInstagram,
            'share-variant': mdiShareVariant,
            'motion-play': mdiMotionPlay,
            'volume-medium': mdiVolumeMedium,
            'volume-high': mdiVolumeHigh,
            'volume-off': mdiVolumeOff,
            'key': mdiKey,
            'credit-card-outline': mdiCreditCardOutline,
            'music-note-eighth': mdiMusicNoteEighth,
            'text-box-search-outline': mdiTextBoxSearchOutline,
            'help': mdiHelp,
            'download': mdiDownload,
            'application-export': mdiApplicationExport,
            'check': mdiCheck,
            'window-close': mdiWindowClose,
            'arrow-all': mdiArrowAll,
            'credit-card-refresh': mdiCreditCardRefresh,
            'file-find': mdiFileFind,
            'ticket': mdiTicket,
            'cash-check': mdiCashCheck,
            'cash-account': mdiAccountCash,
            'shopping': mdiShopping,
            'play-speed': mdiPlaySpeed,
            'music-circle': mdiMusicCircle,
            'email-multiple': mdiEmailMultiple,
            'account-cash-outline': mdiAccountCashOutline,
            'refresh': mdiRefresh
        }
    }
};
const vuetify = new Vuetify()

Vue.use(Vuetify);

Vue.use(TiptapVuetifyPlugin, {
    vuetify,
    iconsGroup: 'mdiSvg'
})

export default new Vuetify(opts);
