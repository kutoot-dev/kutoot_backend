import React, { useEffect, useState, useContext } from 'react';
import axios from "axios";

import { ChatContext } from '../contexts/Chat';

function SingleCustomer({customer}) {
    let root_url = process.env.MIX_APP_URL;
    let image_path = root_url+"/public/";

    const {defaultAvatar , loadMessage, activeCustomer} = useContext(ChatContext)

    let [pending, setPending] = useState(0);

    useEffect(() => {
        setPending(customer.unread_message);
    },[customer]);

    const showMessageBox = (customerId) =>{
        setPending(0)
        loadMessage(customerId)
    }

    return (
        <div>
            <li onClick={() => showMessageBox(customer.customer_id)} className={`media mt-2 customer_msg_list ${activeCustomer.id === customer.customer_id ? 'active' : ''}`  }>
                {customer.customer_image !=null ? <img alt="image" className="mr-3 ml-3 rounded-circle" src={image_path+customer.customer_image} /> : <img alt="image" className="mr-3 ml-3 rounded-circle" src={image_path+defaultAvatar} />}

                {pending != 0 ? <span className="pending" id="pending-">{pending}</span> : ''}

                <div className="media-body mt-4">
                    <div className="font-weight-bold">{customer.customer_name}</div>
                </div>
            </li>
        </div>
    );
}

export default SingleCustomer;
