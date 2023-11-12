self.addEventListener("push", (e) => {
    const notification = e.data.json();
    const startTime = new Date(notification.timings.startTime * 1000);
    const endTime = new Date(notification.timings.endTime * 1000);

    e.waitUntil(self.registration.showNotification(notification.title, {
        body: `${notification.body}\nTimings: ${formatAMPM(startTime)}-${formatAMPM(endTime)}`,
        icon: notification.icon,
        data: {
            notifUrl: notification.notifUrl,
            timings: notification.timings
        }
    }));
});

self.addEventListener("notificationClick", (e) => {
    waitUntil(clients.openWindow(e.notification.data.notifUrl));
});

/**
 * @param {Date} date - The date object to format
 * @returns The date object formated in 12 hour clock as string
 */
function formatAMPM(date) {
    var hours = date.getHours();
    var minutes = date.getMinutes();
    var ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12; // the hour '0' should be '12'
    minutes = minutes < 10 ? '0' + minutes : minutes;
    var strTime = hours + ':' + minutes + ' ' + ampm;
    return strTime;
}
