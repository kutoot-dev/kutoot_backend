import React from 'react';
import ReactDOM from 'react-dom';
import ChatContextProider from '../contexts/Chat';
import Chat from '../components/Chat'

function MainApp() {
    return (
        <ChatContextProider>
            <Chat/>
        </ChatContextProider>
    );
}

export default MainApp;

if (document.getElementById('app')) {
    var dataFromBlade = document.getElementById('app').getAttribute('data');
    ReactDOM.render(<MainApp dataFromBlade={dataFromBlade} />, document.getElementById('app'));
}
