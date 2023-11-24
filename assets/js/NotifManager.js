const NotifManager = {
    /** 
     * Register Service worker
     * @param {string} serviceWorkerUrl - The URL path to the file of the service worker
     */
    registerSw(serviceWorkerUrl) {
        navigator.serviceWorker.register(serviceWorkerUrl);
    },
    /**
     * Request permission to subscribe for notification
     */
    requestSubscribe(callback = () => null, errorCallback = () => null) {
        //Request notification permission 
        Notification.requestPermission().then((permission) => {
            //On permission granted
            if (permission === "granted") {
                //Get service wroker
                navigator.serviceWorker.ready.then((sw) => {
                    this.requestPublicVapid((publicVapidKey) => {
                        sw.pushManager.subscribe({
                            userVisibleOnly: true,
                            applicationServerKey: publicVapidKey
                        })
                            .then((subscription) => {
                                callback(subscription.toJSON());
                            })
                            .catch((reason) => {
                                errorCallback(reason);
                            })
                    })
                })
            }
            //On permission not granted
            else {
                errorCallback("Permission not granted to subscribe");
            }
        })
    },
    /**
     * Request the public VAPID keys from the backend
     * @param {Function} callback - The function to run on request sucess
     * @param {Function} errorCallback - The function to run on request error
     */
    requestPublicVapid(callback, errorCallback = () => null) {
        $.ajax({
            type: "GET",
            url: `${__project_url__}/includes/homeApi.php`,
            data: {
                feature: "getVapid"
            },
            success: (publicVapidKey) => {
                callback(publicVapidKey);
            },
            error: (response) => {
                errorCallback(response.responseText);
            }
        });
    }
}