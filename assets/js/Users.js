const Users = {
    /**
     * This method will encrypt and send the user data to the backend.
     * 
     * @param {string} username 
     * @param {string} password 
     * @param {string} email 
     * @param {string} subscription the subscription should be aquired using the NotifManager
     */
    createUser(username, password, email, subscription) {
        this.getPublicKey((publicKey) => {
            console.log(publicKey);
        })
    },
    getPublicKey(success = (publicKey = "") => null, error = (response = "") => null) {
        $.ajax({
            type: "GET",
            url: `${__project_url__}/includes/homeApi.php`,
            data: {
                feature: "getPublicKey"
            },
            success: function (publicKey) {
                success(publicKey);
            },
            error: function (response) {
                error(response);
            }
        });
    }
}