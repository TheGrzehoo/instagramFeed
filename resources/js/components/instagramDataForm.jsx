import React, { useState } from 'react';
import axios from 'axios';
import Alert from './util/Alert';

const InstagramDataForm = (props) => {
    const [error, setError] = useState({
        displayError: false,
        alertData: {
            message: null,
            action: null,
            actionText: null,
            actionHref: null,
            fixed: true,
            class: 'primary',
        }
    });
    function copyToClipboard() {
        const copyText = document.getElementById("copyUrl");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        document.execCommand("copy");
        alert("Copied the text: " + copyText.value);
    }
    function saveClientData(e) {
        e.preventDefault();
        console.log('loading');
        const appID = document.querySelector('#appID').value;
        const appSecret = document.querySelector('#appSecret').value;
        axios.post('/api/updateClient', {
            appID: appID,
            appSecret: appSecret,
        }).then(function () {
            axios.get('/api/generateToken').then((obj) => {
                if (obj.data.error) {
                    setError({
                        displayError: true,
                        alertData: {
                            message: 'Wprowadzono niepoprawne dane. ',
                            action: () => {
                                setError({
                                    displayError: false,
                                    alertData: {
                                        message: null,
                                        action: null,
                                        actionText: null,
                                        actionHref: null,
                                        fixed: true,
                                        class: 'primary',
                                    }
                                });
                                return true;
                            },
                            actionText: 'Zamknij',
                            actionHref: '##',
                            fixed: true,
                            class: 'danger',
                        }
                    })
                } else {
                    props.loadedInfo({
                        reloadPage: true,
                        clientData: 'loading',
                    })
                }
            })
        })
        return false;
    }

    const alert = <Alert
        message={error.alertData.message}
        action={error.alertData.action}
        actionText={error.alertData.actionText}
        actionHref={error.alertData.actionHref}
        fixed={error.alertData.fixed}
        class={error.alertData.class}
    />
    return (
        <div className="row">
            <div className="col-md-6 mx-auto">
                <form onSubmit={(e) => saveClientData(e)}>
                    <div className="form-group">
                        <label htmlFor="appID">App ID</label>
                        <input type="text" required={true} className="form-control" id="appID" aria-describedby="appIDhelp" />
                        <small id="appIDhelp" className="form-text text-muted">Dane z pola Instagram App ID</small>
                    </div>
                    <div className="form-group">
                        <label htmlFor="appSecret">App secret key</label>
                        <input type="password" required={true} className="form-control" id="appSecret" aria-describedby="appSecretHelp" />
                        <small id="appSecretHelp" className="form-text text-muted">Dane z pola Instagram App Secret</small>
                    </div>
                    <button type="submit" className="btn btn-primary">Zatwierdź</button>
                </form>
                <div className="form-group mt-4">
                    <label htmlFor="appSecret">Do pól 'Client OAuth Settings', 'Deauthorize', 'Data Deletion Requests' wpisz poniższy ardes:</label>
                    <div className="input-group mb-3">
                        <input readOnly id="copyUrl" type="text" className="form-control" value="https://127.0.0.1:8000/clientCodeHandler" aria-label="Recipient's username" aria-describedby="button-addon2" />
                        <div className="input-group-append">
                            <button className="btn btn-outline-secondary" onClick={() => copyToClipboard()} type="button" id="button-addon2">Kopiuj</button>
                        </div>
                    </div>
                </div>
            </div>
            {error.displayError && alert}
        </div>
    );
}

export default InstagramDataForm;