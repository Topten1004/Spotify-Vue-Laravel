<template>
    <div class="landing-container">
        <header
            class="landing-header relative custom"
            :style="{
                backgroundImage:
                    'url(' +
                    (landingPage.headerBackground || '/images/landing-background.jpg') +
                    ')'
            }"
        >
            <div
                class="landing-header__cover-layer absolute fill"
                :style="{
                    backgroundColor: landingPage.headerBackgroundLayerColor
                }"
            ></div>
            <nav class="landing-header__nav">
                <div class="logo">
                    <img
                        :src="$store.getters.getSettings.appLogo"
                        :alt="$t('Logo Image')"
                    />
                </div>
                <div class="options">
                    <div class="user white--text" v-if="user">
                        <User></User>
                    </div>
                    <div class="auth-buttons" v-else>
                        <div class="buttons">
                            <v-btn
                                class="primary white--text"
                                small
                                @click="$router.push({ name: 'login' })"
                                >{{ $t("Login") }}</v-btn
                            >
                            <v-btn
                                class="register__btn ml-2 white--text"
                                small
                                v-if="!$store.getters.getSettings.disableRegistration"
                                outlined
                                color="white"
                                @click="$router.push({ name: 'register' })"
                                >{{ $t("Register") }}</v-btn
                            >
                        </div>
                    </div>
                </div>
            </nav>
            <div class="absolute fill text-center z-index-0">
                <div class="landing-header__main">
                    <div class="landing-header__text">
                        <h1>{{ landingPage.headerTitle }}</h1>
                        <h2>{{ landingPage.headerDescription }}</h2>
                    </div>
                </div>
                <div class="landing-header__button">
                    <v-btn
                        class="primary"
                        rounded
                        large
                        :to="$store.getters.getSettings.playerLanding"
                    >
                        {{ landingPage.callToAction }}
                    </v-btn>
                </div>
            </div>
        </header>
        <!-- old header -->
        <!-- <header class="landing-header default" v-else>
            <nav class="landing-header__nav">
                <div class="logo">
                    <img
                        :src="$store.getters.getSettings.appLogo"
                        :alt="$t('Logo Image')"
                    />
                </div>
                <div class="options">
                    <div class="user white--text" v-if="user">
                        <div class="account" @click="userMenu = !userMenu">
                            <div class="avatar">
                                <img
                                    :src="user.avatar"
                                    alt=""
                                    class="avatar-image"
                                />
                            </div>
                            <div class="name">
                                {{ user.name }}
                            </div>
                            <div class="short-name">
                                {{ user.name.match(/\w+/)[0] }}
                            </div>
                            <div class="chevron">
                                <v-icon small>$vuetify.icons.chevron-down</v-icon>
                            </div>
                            <abs-menu
                                :style="{
                                    top: '105%',
                                    width: '12em',
                                    right: '0'
                                }"
                                theme="light"
                                v-if="userMenu"
                            >
                                <user-menu
                                    :isAdmin="isAdmin(user)"
                                    :isArtist="isArtist(user)"
                                    :userId="user.id"
                                    @click:outside="userMenu = !userMenu"
                                />
                            </abs-menu>
                        </div>
                    </div>
                    <div class="auth-buttons" v-else>
                        <div class="buttons">
                            <v-btn
                                class="primary white--text"
                                small
                                @click="$router.push({ name: 'login' })"
                                >{{$t('Login')}}</v-btn
                            >
                            <v-btn
                                class="register__btn ml-2 white--text"
                                small
                                outlined
                                color="white"
                                @click="$router.push({ name: 'register' })"
                                >{{$t('Register')}}</v-btn
                            >
                        </div>
                    </div>
                </div>
            </nav>
            <div class="relative text-center">
                <div class="landing-header__main">
                    <div class="landing-header__default-text">
                       <h1>{{$t('Play')}} {{$t('Music')}}</h1>
                        <h2>{{$t('anywhere')}}, {{$t('anytime')}}.</h2>
                    </div>
                    <div class="landing-header__hero">
                        <img src="/images/hero.png" />
                    </div>
                </div>
                <div class="landing-header__button">
                    <v-btn
                        class="primary"
                        rounded
                        large
                        :to="$store.getters.getSettings.playerLanding"
                    >
                        {{ landingPage.callToAction }}
                    </v-btn>
                </div>
            </div>
        </header> -->
        <main>
            <section
                class="landing-section"
                v-for="(section, i) in landingPage.sections"
                :key="i"
            >
                <v-container class="container">
                    <v-row align="center">
                        <v-col
                            lg="6"
                            md="6"
                            sm="12"
                            class="justify-center"
                            v-if="section.background"
                            :style="{ order: i % 2 == 0 ? 1 : 0 }"
                        >
                            <div class="image-side">
                                <img :src="section.background" />
                            </div>
                        </v-col>
                        <v-col
                            :lg="section.background ? 6 : 12"
                            :md="section.background ? 6 : 12"
                            sm="12"
                            class="text-side-container justify-center"
                        >
                            <div class="text-side">
                                <div class="landing-section__title">
                                    {{ section.title }}
                                </div>
                                <div class="landing-section__sub-title">
                                    {{ section.description }}
                                </div>
                            </div>
                        </v-col>
                    </v-row>
                </v-container>
                <v-divider class="sections-divider"></v-divider>
            </section>
            <section
                class="landing-section"
                v-if="
                    landingPage.showContactUs &&
                        $store.getters.getSettings.enableMail
                "
            >
                <v-container class="container">
                    <h2 class="text-center">{{ $t("Contact Us") }}</h2>
                    <v-row>
                        <v-col class="justify-center">
                            <contact-us />
                        </v-col>
                    </v-row>
                </v-container>
            </section>
        </main>
        <footer>
            <div
                class="footer-container"
                :style="{
                    backgroundImage: 'url(' + landingPage.footerBackground + ')'
                }"
            >
                <v-row>
                    <v-col class="text-center">
                        <div class="footer__title">
                            <h2>
                                {{ landingPage.footerTitle }}
                            </h2>
                        </div>
                        <div class="footer__sub-title">
                            {{ landingPage.footerDescription }}
                        </div>
                        <v-btn
                            color="white"
                            class="mt-2"
                            outlined
                            min-width="120"
                            v-if="!$store.getters.getSettings.disableRegistration"
                            @click="$router.push({ name: 'register' })"
                            >{{ $t("Register") }}</v-btn
                        >
                    </v-col>
                </v-row>
            </div>
        </footer>
        <div class="text-center py-4">
            © {{ moment().year() }} {{ $store.getters.getSettings.appName }}. {{ $t('All rights reserved.') }}
        </div>
    </div>
</template>

<script>
import ContactUs from "./dialogs/ContactUs";
import User from "./elements/User.vue";
export default {
    metaInfo() {
        return {
            meta: [
                {
                    name: "description",
                    content: this.$store.getters.getSettings.siteDescription.replace("%site_name", this.$store.getters.getSettings.appName)
                }
            ]
        };
    },
    components: {
        ContactUs,
        User
    },
    created() {
        this.landingPage.sections = JSON.parse(this.landingPage.sections);
        this.landingPage.showContactUs = Boolean(
            parseInt(this.landingPage.showContactUs)
        );
    },
    data() {
        return {
            landingPage: JSON.parse(JSON.stringify(this.$store.getters.getSettings.landingPage)),
            userMenu: false
        };
    },
    computed: {
        user() {
            return this.$store.getters.getUser;
        }
    }
};
</script>

<style lang="scss">
.landing-container {
    background-color: #fdfdfd;
    min-width: 350px;
    color: #0f0f0f;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    main {
        flex-grow: 1;
    }
}
.landing-header.custom {
    height: 90vh;
    @media (max-width: 900px) {
        height: 50vh;
    }
}
.landing-header.default {
    background-image: url("/images/background.png");
}
.landing-header {
    background-size: cover;
    background-position: center center;
    border-bottom-right-radius: 160% 40%;
    border-bottom-left-radius: 160% 40%;
    color: white;
    margin-bottom: 3em;
    &__nav {
        padding: 1em 1.5em;
        display: flex;
        z-index: 2;
        justify-content: space-between;
        align-items: center;
        .logo {
            padding-left: 1em;
            z-index: 2;
            img {
                max-height: 3em;
            }
        }
        .options {
            display: flex;
            z-index: 2;
        }
    }
    &__cover-layer {
        z-index: 0;
        border-bottom-right-radius: 160% 40%;
        border-bottom-left-radius: 160% 40%;
    }
    &__hero {
        text-align: center;
        padding-top: 5rem;
        display: flex;
        justify-content: center;
        img {
            width: 27%;
            min-width: 180px;
        }
    }
    &__main {
        overflow: hidden;
        border-bottom-right-radius: 150% 40%;
        border-bottom-left-radius: 150% 40%;
    }
    &__text {
        position: absolute;
        left: 0;
        right: 0;
        top: 50%;
        transform: translateY(-50%);
        text-align: center;
        position: absolute;
        color: #fff;
        h1 {
            font-size: 4em;
        }
        h2 {
            font-size: 1.25em;
            font-weight: 400;
        }
    }
    &__default-text {
        position: absolute;
        left: 0;
        right: 0;
        top: 50%;
        transform: translateY(-50%);
        text-align: center;
        position: absolute;
        color: #fff;
        h1 {
            font-weight: 800;
            word-spacing: 4em;
            white-space: nowrap;
            font-size: 6em;
            padding-left: 1em;
            text-transform: uppercase;
            margin-bottom: 2em;
        }
        h2 {
            text-align: center;
            font-size: 2.5em;
            font-weight: 500;
            z-index: 2;
            position: relative;
        }

        @media (max-width: 1920px) {
            top: 60%;
        }
        @media (max-width: 1500px) {
            top: 60%;
            h1 {
                word-spacing: 3em;
                padding-left: 0.55em;
            }
        }
        @media (max-width: 1300px) {
            top: 65%;
            h1 {
                word-spacing: 2em;
                margin-bottom: 1em;
                padding-left: 0.95em;
            }
        }
        @media (max-width: 950px) {
            top: 65%;
            h1 {
                margin-bottom: 0.75em;
                font-size: 5em;
            }
            h2 {
                font-size: 1.75em;
            }
        }
        @media (max-width: 750px) {
            top: 55%;
            h1 {
                font-size: 4em;
                padding-left: 0.45em;
            }
        }
        @media (max-width: 600px) {
            top: 60%;
            h1 {
                font-size: 3em;
            }
        }
        @media (max-width: 500px) {
            h1 {
                word-spacing: 2.5em;
                padding-left: 0.65em;
            }
        }
        @media (max-width: 400px) {
            top: 50%;
            h1 {
                word-spacing: 1.25em;
            }
        }
    }
    &__button {
        position: absolute;
        left: 0;
        right: 0;
        z-index: 2;
        bottom: 0;
        text-align: center;
        transform: translateY(50%);
        .v-btn {
            max-width: 14em;
            width: 30%;
        }
    }
}
.landing-section {
    padding: 1em 4em;
    .container {
        max-width: 1200px !important;
    }
    &__title {
        font-size: 2em;
        font-weight: 700;
        line-height: 1.15;
        margin-bottom: 0.75em;
    }
    &__sub-title {
        opacity: 0.75;
    }
    .text-side-container {
        @media (max-width: 960px) {
            text-align: center;
            order: -1;
        }
        .text-side {
            max-width: 500px;
        }
    }
    .image-side {
        img {
            max-width: 650px;
            min-width: 350px;
            @media (max-width: 1300px) {
                max-width: 550px; 
            }
            @media (max-width: 960px) {
                width: 110%;
            }
        }
        padding: 1.5em;
        display: flex;
        justify-content: center;
    }
}
.footer-container {
    padding: 3em;
    background-size: cover;
    color: white;
    .footer {
        &__title {
            font-size: 1.2em;
        }
        &__sub-title {
            font-weight: 400;
            opacity: 0.75;
            line-height: 2;
        }
    }
}
.z-index-0 {
    z-index: 0;
}

.sections-divider {
    @media (min-width: 960px) {
        display: none !important;
    }
}

.account {
    display: flex;
    align-items: center;
    border-radius: 25px;
    padding: 0.3rem;
    transition: all 0.4s;
    position: relative;
    cursor: pointer;
    &:hover {
        background-color: rgba(110, 110, 110, 0.226);
    }

    .avatar {
        width: 35px;
        margin-right: 0.3rem;
        &-image {
            height: 100%;
            border-radius: 50%;
            width: 100%;
        }
    }

    .short-name {
        @media screen and(min-width: 900px) {
            display: none;
        }

        @media screen and(max-width: 700px) {
            display: none;
        }
    }

    .name {
        max-width: 6em;
        @media screen and(max-width: 900px) {
            display: none;
        }
    }

    .chevron {
        margin-left: 0.5rem;
    }
}
</style>
