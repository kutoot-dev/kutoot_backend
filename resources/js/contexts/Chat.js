import { createContext, useState, useEffect } from 'react'
import axios from "axios";


export const ChatContext = createContext();

function ChatContextProider(props){

    let root_url = process.env.MIX_APP_URL;
    let image_path = root_url+"/public/";


    const [customers, setCustomer] = useState([]);
    const [isLoadMessageBox, setLoadMessageBox] = useState(false);
    const [messages, setMessages] = useState([]);
    const [defaultAvatar, setDefaultAvatar] = useState('');
    const [authUser, setAuthUser] = useState('');
    const [activeCustomer, setActiveCustomer] = useState({
        id : '',
        name : '',
        email : '',
        image : ''
    });
    const [chatCustomerId, setChatCustomerId] = useState('');

    useEffect(() => {
        axios.get(`${root_url}/seller/message-customer-list`)
            .then(res => {
                setCustomer(res.data.customer_list);
                setDefaultAvatar(res.data.default_avatar);
                setAuthUser(res.data.auth_user);
            })
            .catch(error => {
            })
    },[]);

    const sendMessageToCustomer = (newMessage) => {
        axios.post(`${root_url}/seller/send-message-to-customer`, newMessage)
            .then(res => {
                setMessages(res.data.messages);
            })
            .catch(error => {

            })
    }

    Echo.private(`user-to-seller-message.${authUser.id}`)
        .listen('UserToSellerMessage', (e) => {

            if(activeCustomer.id != ''){
                const item = JSON.parse(localStorage.getItem('customer'));
                if(item){
                    if(parseInt(item.id) ===  parseInt(e.message.customer_id)){
                        const item = JSON.parse(localStorage.getItem('customer'));
                        axios.get(`${root_url}/seller/load-active-user-message/${item.id}`,)
                            .then(res => {
                                setMessages(res.data.messages);
                            })
                            .catch(error => {

                            })
                    }else{
                        axios.get(`${root_url}/seller/message-customer-list`)
                            .then(res => {
                                setCustomer(res.data.customer_list);
                            })
                            .catch(error => {

                            })
                    }
                }


            }else{
                axios.get(`${root_url}/seller/message-customer-list`)
                    .then(res => {
                        setCustomer(res.data.customer_list);
                    })
                    .catch(error => {

                    })
            }
        });


        const loadMessage = (id) => {
            axios.get(`${root_url}/seller/load-active-user-message/${id}`,)
                .then(res => {
                    setMessages(res.data.messages);
                })
                .catch(error => {

                })

            let findCustomer = customers.filter(customer => customer.customer_id === id);
            let newCustomer = {
                id : findCustomer[0].customer_id,
                name : findCustomer[0].customer_name,
                email : findCustomer[0].customer_email,
                image : findCustomer[0].customer_image
            };
            setActiveCustomer(newCustomer)
            setLoadMessageBox(true);
            setChatCustomerId(id);

            localStorage.setItem('customer', JSON.stringify(newCustomer));

        }


    return(
        <ChatContext.Provider value={{ customers, isLoadMessageBox, messages, defaultAvatar, loadMessage, activeCustomer, authUser, sendMessageToCustomer }}>
            {props.children}
        </ChatContext.Provider>
    )
}

export default ChatContextProider;
