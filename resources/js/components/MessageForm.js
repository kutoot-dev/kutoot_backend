import React, { useState, useContext } from 'react';

import { ChatContext } from '../contexts/Chat';

function MessageForm({customer}) {
    let root_url = process.env.MIX_APP_URL;
    let image_path = root_url+"/public/";

    const {isLoadMessageBox, activeCustomer, sendMessageToCustomer} = useContext(ChatContext)

    const [message, setMessage] = useState('');

    const submitMessageFrom = (event) => {
        event.preventDefault();

        let newMessage = {
            customer_id : activeCustomer.id,
            message : message
        }

        sendMessageToCustomer(newMessage);
        setMessage('');
    }
    return (
        <div className="card-footer chat-form">
            {isLoadMessageBox &&
                <form onSubmit={submitMessageFrom}>
                    <input type="text" className="form-control" value={message} onChange={(e) => setMessage(e.target.value)} placeholder="Type here..." />

                    <button type="submit" className="btn btn-primary">
                    <i className="far fa-paper-plane"></i>
                    </button>
                </form>
            }
        </div>

    );
}

export default MessageForm;
