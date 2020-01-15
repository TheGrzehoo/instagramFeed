import React from 'react';

const InstagramDataForm = () => {
    function myFunction() {
        const copyText = document.getElementById("copyUrl");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        document.execCommand("copy");
        alert("Copied the text: " + copyText.value);
    }
    return (
        <div className="col-md-8">
            <form>
                <div className="form-group">
                    <label htmlFor="appID">App ID</label>
                    <input type="email" className="form-control" id="appID" aria-describedby="appIDhelp" />
                    <small id="appIDhelp" className="form-text text-muted">Dane z pola Instagram App ID</small>
                </div>
                <div className="form-group">
                    <label htmlFor="appSecret">App secret key</label>
                    <input type="password" className="form-control" id="appSecret" aria-describedby="appSecretHelp" />
                    <small id="appSecretHelp" className="form-text text-muted">Dane z pola Instagram App Secret</small>
                </div>
                <button type="submit" className="btn btn-primary">Zatwierdź</button>
            </form>
            <div className="form-group mt-4">
                <label htmlFor="appSecret">Do pól 'Client OAuth Settings', 'Deauthorize', 'Data Deletion Requests' wpisz poniższy ardes:</label>
                <div className="input-group mb-3">
                    <input readOnly id="copyUrl" type="text" className="form-control" value="https://127.0.0.1:8000/clientCodeHandler" aria-label="Recipient's username" aria-describedby="button-addon2" />
                    <div className="input-group-append">
                        <button className="btn btn-outline-secondary" onClick={() => myFunction()} type="button" id="button-addon2">Kopiuj</button>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default InstagramDataForm;