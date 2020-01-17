import React from 'react';
import ReactDOM from 'react-dom';
import InstagramDataForm from './instagramDataForm';
import axios from 'axios';
import { useState } from 'react';
import ManageInstaFeed from './ManageInstaFeed';
import Loading from './Loading';


export default function App () {
    const [userInfo, setUserInfo] = useState({
        clientData: 'loading',
        reloadPage: true
    });

    let pageContent = function() {
        switch (userInfo.clientData) {
            case 'loading':
                return <Loading />
            case true:
                return <ManageInstaFeed refreshClient={setUserInfo} />
            case false:
                return <InstagramDataForm loadedInfo={setUserInfo} />
            default:
                return 'Refresh Page and try again'
                break;
        }
    }

    if(userInfo.reloadPage){
        axios.get('/isClientSaved').then((obj) => {
            console.log(obj);
            setUserInfo({
                ...userInfo,
                clientData: obj.data,
                reloadPage: false
            })
        })
    }
    

    return (
        <div className="container" style={{width: '100%'}}>
            <div className="row justify-content-center">
            {pageContent()}
            </div>
        </div>
    );
}

ReactDOM.render(<App />, document.getElementById('app'));