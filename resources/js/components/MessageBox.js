import React, { useContext, useRef, useEffect } from 'react';
import TimeAgo from 'javascript-time-ago'
import ReactTimeAgo from 'react-time-ago'

import { ChatContext } from '../contexts/Chat';

import MessageForm from '../components/MessageForm';

import en from 'javascript-time-ago/locale/en.json'
import ru from 'javascript-time-ago/locale/ru.json'


TimeAgo.addDefaultLocale(en)
TimeAgo.addLocale(ru)

function MessageBox({customer}) {
    let root_url = process.env.MIX_APP_URL;
    let image_path = root_url+"/public/";

    const {defaultAvatar , isLoadMessageBox, activeCustomer , messages, authUser} = useContext(ChatContext)

    const messageRef = useRef(null);
    const fixedScrollBottom = () => {
        const messages = messageRef.current;
        messages.scrollTop = messages.scrollHeight;
    }

    useEffect(() => {
        fixedScrollBottom();
    },[messages]);

    const convertTime = (time) =>{
        let newDate = new Date(time);
        newDate = newDate.getTime();

        return newDate;
    }

    const loadEditProduct = (id) => {
        window.open(`${process.env.MIX_APP_URL}/seller/product/${id}/edit`, '_blank');
    }
    return (
        <div className="card chat-box" >
            <div className="card-header">
                {isLoadMessageBox &&  <h4>Chat with {activeCustomer.name}</h4> }

            </div>

            <div className="card-body chat-content" ref={messageRef}>
                {messages.map(message => {
                    return message.send_by == 'seller' ? (
                        <div key={message.id} className="chat-item chat-right">
                            {authUser.image !=null ? <img alt="image" src={image_path+authUser.image} /> : <img alt="image"  src={image_path+defaultAvatar} />}

                            <div className="chat-details">
                                <div className="chat-text">{message.message}</div>
                                <div className="chat-time">

                                    <ReactTimeAgo date={convertTime(message.created_at)} locale="en-US"/>


                                    </div>
                            </div>
                        </div>
                    ) : (
                        <div key={message.id} className={`chat-item chat-left ${message.product ? 'chat_product' : ''}`}>

                        {activeCustomer.image !=null ? <img alt="image" src={image_path+activeCustomer.image} /> : <img alt="image"  src={image_path+defaultAvatar} />}

                            <div className="chat-details">

                            <a className={`product_img ${message.product ? '' : 'd-none'}`} href="javascript:;" onClick={() => loadEditProduct(message.product_id)}>
                                <img src={message.product ? image_path+message.product.thumb_image : ''} alt="product" />
                                <p>{message.product ? message.product.name : ''}</p>
                            </a>

                                <div className="chat-text">{message.message}</div>
                                <div className="chat-time"><ReactTimeAgo date={convertTime(message.created_at)} locale="en-US"/></div>
                            </div>
                        </div>
                    )
                })}
            </div>

            <MessageForm/>

        </div>
    );
}

export default MessageBox;
