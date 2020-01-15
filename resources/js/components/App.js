import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import InstagramDataForm from './instagramDataForm';

export default class App extends Component {
    render() {
        return (
            <div className="container">
                <div className="row justify-content-center">
                    <InstagramDataForm/>
                </div>
            </div>
        );
    }
}

if (document.getElementById('app')) {
    ReactDOM.render(<App />, document.getElementById('app'));
}
