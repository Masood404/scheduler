const Users = {
    /**
     * This method will encrypt and send the user data to the backend.
     * 
     * @param {string} username 
     * @param {string} password 
     * @param {string} email 
     * @param {string} subscription the subscription should be aquired using the NotifManager
     * @returns {Promise} if the promise is resolved pass in a parameter to the then method to get the response
     */
    createUser(username, password, email = null, subscription = null) {
        return new Promise((reslove, reject) => {
            this.getPublicKey()
                .then((publicKey) => {
                    const encrypt = new JSEncrypt();
                    encrypt.setPublicKey(publicKey);

                    const user_data = JSON.stringify({
                        username: username,
                        password: password,
                        email: email,
                        subscription: subscription
                    });

                    //Encrypt the user data
                    const encrypted_user_data = encrypt.encrypt(user_data);

                    $.ajax({
                        type: "POST",
                        url: `${__project_url__}/includes/homeApi.php`,
                        data: {
                            userData: encrypted_user_data
                        },
                        success: function (response) {
                            reslove(response);
                        },
                        error: function (response) {
                            reject(response.responseText);
                        }
                    });

                })
                .catch((response) => {
                    reject(response)
                })
        })
    },
    /**
     * 
     * @returns {Promise} if the promise is resolved pass in a parameter to the then method to get the public key
     */
    getPublicKey() {
        return new Promise((reslove, reject) => {
            $.ajax({
                type: "GET",
                url: `${__project_url__}/includes/homeApi.php`,
                data: {
                    feature: "getPublicKey"
                },
                success: function (publicKey) {
                    reslove(publicKey);
                },
                error: function (response) {
                    reject(response);
                }
            });
        })
    }
}