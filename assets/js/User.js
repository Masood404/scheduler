/*
    Make sure to also include the libraries CryptoJS and JSEncrypt in the document using script tags.
*/

const User = {
    /**
     * This method will encrypt and send the user data to the backend.
     * 
     * @param {string} username 
     * @param {string} password 
     * @param {string} remember 
     * @param {string} email 
     * @param {PushSubscription} subscription the subscription should be aquired using the NotifManager
     * @returns {Promise<void>} The promise will resolve into nothing, it will set the authToken and reload.
     */
    async create(username, password, remember = false, email = null, subscription = null) {
        try {
            if (localStorage.getItem("authToken")) {
                throw "User already logged in!";
            }
            if (!this.validateUsername(username) || !this.validatePassword(password)) {
                throw "Invalid username or password!";
            }
            if (email != null && !this.validateEmail(email)) {
                throw "Invalid email!";
            }

            let userData = JSON.stringify({
                username: username,
                password: password,
                email: email,
                remember: remember
            });
            //Encrypt user data with RSA
            userData = await this.crypto.encrypt(userData);

            let aesKey = null;
            if (subscription != null) {
                //Parse the subscription to string
                subscription = JSON.stringify(subscription.toJSON());

                //Encrypt the subscription using AES instead of RSA
                aesKey = this.crypto.generateAESKey();
                subscription = this.crypto.encryptWithAES(subscription, aesKey);

                //Encrypt the AES key with the RSA key
                aesKey = await this.crypto.encrypt(aesKey);
            }

            const response = await $.ajax({
                type: "POST",
                url: `${__project_url__}/api/users/create.php`,
                data: {
                    createData: userData,
                    aesKey: aesKey,
                    subscription: subscription
                }
            });

            const authToken = response.authToken;

            localStorage.setItem("authToken", authToken);
            localStorage.setItem("newAuthToken", true);

            location.reload();

        }
        catch (error) {
            if (error.responseText) {
                throw error.responseText;
            }
            throw error;
        }
    },
    /**
     * This method will get the autorization token and set it in the localstorage.
     * @param {string} username 
     * @param {string} password 
     * @param {boolean} remember If true the authorization token will be valid for 30 days.
     * @return {Promise<void>} The will not resolve, it will set the authToken in localstorage and reload.
     */
    async login(username, password, remember = false) {
        try {
            if (localStorage.getItem("authToken")) {
                throw "User already logged in!";
            }
            if (!this.validateUsername(username)) {
                throw "Invalid username!";
            }
            else if (!this.validatePassword(password)) {
                throw "Invalid password!";
            }

            let loginData = JSON.stringify({
                username: username,
                password: password,
                remember: remember
            });

            loginData = await this.crypto.encrypt(loginData);

            const response = await $.ajax({
                type: "POST",
                url: `${__project_url__}/api/users/login.php`,
                data: {
                    loginData: loginData
                },
            });

            localStorage.setItem("authToken", response.authToken);
            localStorage.setItem("newAuthToken", true);

            location.reload();
        }
        catch (error) {
            if (error.responseText) {
                throw error.responseText;
            }
            throw error;
        }
    },
    logout: logout,
    /**
     * Used to validate a username
     * @param {string} username 
     */
    validateUsername(username) {
        const usernamePattern = /^[a-zA-Z0-9_-]{3,32}$/;

        return usernamePattern.test(username);
    },
    /**
     * Used to validate a password
     * @param {string} password 
     */
    validatePassword(password) {
        const passwordPattern = /^(?=.*[A-Za-z0-9])[A-Za-z0-9\d@$_#\-]{8,}$/;

        return passwordPattern.test(password)
    },
    /**
     * Used to validate an email
     * @param {string} email 
     */
    validateEmail(email) {
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        return emailPattern.test(email);
    },
    /**
     * Property to encrypt user data.
     */
    crypto: {
        //To be cached
        publicKey: null,
        /**
         * RSA public encryption
         * @param {string} data The data to encrypt.
         * @returns {Promise<encData>} A Promise which resolves into an encData string.
         */
        async encrypt(data) {
            try {
                let publicKey;
                if (this.publicKey != null) {
                    //If cached then use it.
                    publicKey = this.publicKey;
                }
                else {
                    publicKey = await this.getPublicKey();
                    //Cache the public key until page reload.
                    this.publicKey = publicKey;
                }

                const encrypt = new JSEncrypt();
                encrypt.setPublicKey(publicKey);

                encData = encrypt.encrypt(data);

                return encData;
            }
            catch (error) {
                throw error;
            }
        },
        /**
         * Get the RSA Public Key
         * @returns {Promise<publicKey>} A promise that resolves into a string publickey
         */
        async getPublicKey() {
            try {
                const response = await $.ajax({
                    type: "GET",
                    url: `${__project_url__}/api/users/get-crypto-keys.php`,
                    data: {
                        key: "rsa"
                    }
                });

                return response.rsaKey;
            }
            catch (error) {
                throw "Error getting the public key: " + error;
            }
        },
        /**
         * Generate an AES key using the CryptoJS library.
         * @returns {string} returns an AES key in base64 string.
         */
        generateAESKey() {
            // Generate a random 256-bit key (32 bytes)
            const key = CryptoJS.lib.WordArray.random(32);

            // Convert the key to a Base64-encoded string
            const keyBase64 = CryptoJS.enc.Base64.stringify(key);

            return keyBase64;
        },
        /**
         * AES encryption using the CryptoJS library.
         * @param {string} data Any form of data should first be parsed into string.
         * @param {string} aesKey The Aes Key should also be parsed into base64 string.
         * @returns {string} The encrypted data in base64 string.
         */
        encryptWithAES(data, aesKey) {
            const parsedKey = CryptoJS.enc.Base64.parse(aesKey);
            let encData = CryptoJS.AES.encrypt(data, parsedKey, {
                mode: CryptoJS.mode.ECB, // Using ECB mode (not recommended for all scenarios)
            });

            //Converting the encrypted data to base64 string.
            encData = encData.toString();

            return encData;
        }
    },
    /**
     * The Notification manager
     */
    notifManager: {
        /** 
         * Register Service worker
         * @param {string} serviceWorkerUrl - The URL path to the file of the service worker
         */
        registerSw(serviceWorkerUrl) {
            navigator.serviceWorker.register(serviceWorkerUrl);
        },
        /**
         * Request subscription from the user.
         * @returns {Promise<subscription>} A promise that resolves into subscription string.
         */
        async requestSubscription() {
            try {
                const permission = await Notification.requestPermission();

                if (permission === "granted") {
                    //Get the service worker
                    const serviceWorker = await navigator.serviceWorker.ready;
                    const publicVapidKey = await this.requestPublicVapid();
                    const subscription = await serviceWorker.pushManager.subscribe({
                        userVisibleOnly: true,
                        applicationServerKey: publicVapidKey
                    });

                    return subscription;
                }
                else {
                    throw "Permission not granted for subscription";
                }
            } catch (error) {
                if (error === "Permission not granted for subscription") {
                    throw error;
                }
                else {
                    throw "Error requesting subscription: " + error;
                }
            }
        },
        /**
         * Request the public VAPID keys from the backend.
         * @returns {Promise<publicVapidKey>} A promise that will resolve into a string publicVapidKey.
         */
        async requestPublicVapid() {
            try {
                const response = await $.ajax({
                    type: "GET",
                    url: `${__project_url__}/api/users/get-crypto-keys.php`,
                    data: {
                        "key": "vapid"
                    }
                });

                return response.vapidKey;
            }
            catch (error) {
                throw error;
            }
        }
    }
}
