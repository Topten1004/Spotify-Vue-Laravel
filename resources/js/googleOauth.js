import store from './store/index';

/**
 * Fetch the user data after a successful signin from Google's OAuth.
 * @param {*} googleUser 
 */
window.onSignIn = function(googleUser) {
    var profile = {
        email: googleUser.getBasicProfile().getEmail(),
        name: googleUser.getBasicProfile().getName(),
        avatar: googleUser.getBasicProfile().getImageUrl()
    };
    store.dispatch("login", { driver: "google", profile }); // This is null if the 'email' scope is not present.
};
/**
 * Signout a user logged with Google's OAuth.
 */
window.signOut = function() {
    var auth2 = gapi.auth2.getAuthInstance();
    auth2.signOut()
};
