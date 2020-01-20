import React from 'react';
import ReactDOM from 'react-dom';
import InstagramDataForm from './instagramDataForm';
import axios from 'axios';
import { useState } from 'react';
import ManageInstaFeed from './ManageInstaFeed';
import Loading from './util/Loading';
import Logo from './Logo';


export default function App () {
    const [userInfo, setUserInfo] = useState({
        clientData: 'loading',
        reloadPage: true
    });

    let pageContent = function() {
        switch (userInfo.clientData) {
            case 'loading':
                return <Loading fixed={true} />
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
        <div id="container" className="container">
            <Logo />
            {pageContent()}
        </div>
    );
}

ReactDOM.render(<App />, document.getElementById('app'));