import React, { useContext } from 'react';
import SingleCustomer from '../components/SingleCustomer';
import MessageBox from '../components/MessageBox';
import { ChatContext } from '../contexts/Chat';

function Chat() {
    const {customers} = useContext(ChatContext)
    return (
        <div className='row justify-content-center'>
            <div className='col-12 col-sm-6 col-lg-4'>
                <div className="card">
                    <div className="card-header">
                        <h4>Customer List</h4>
                    </div>

                    <div className="card-body seller_chat_list">
                        <ul className="list-unstyled list-unstyled-border">
                            {customers.map(customer=>{
                                return <SingleCustomer key={customer.customer_id} customer={customer}/>
                            })}
                        </ul>
                    </div>

                </div>
            </div>
            <div className='col-12 col-sm-6 col-lg-8'>
                <MessageBox/>
            </div>

        </div>
    );
}

export default Chat;
