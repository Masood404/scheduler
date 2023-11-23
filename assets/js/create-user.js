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

    },
    getPublicKey() {
        return $.ajax({
            type: "GET",
            url: "",
            data: "data",
            dataType: "dataType",
            success: function (response) {

            }
        });
    }
}