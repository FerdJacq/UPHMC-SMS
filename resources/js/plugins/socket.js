
import io from 'socket.io-client';
// location.protocol + '//' + window.location.hostname + ":8890"
const socket_url = 'https://' + window.location.hostname +":8890";

const socket = io(socket_url,
{
    autoConnect:false,
    reconnectionDelay: 1000,
    reconnection:true,
    reconnectionAttempts: Infinity,
    transports: ['websocket'],
    agent: false, // [2] Please don't set this to true
    upgrade: false,
    rejectUnauthorized: false
});

export default socket