import React from 'react';

const Alert = (props) => {
    let alertWindowStyles = {
        position: 'fixed',
        top: 0,
        left: 0,
        right: 0,
        bottom: 0,
        background: 'rgba(255,255,255,0.6)',
        zIndex: 999,
    }
    if(props.fixed) {
        console.log(alertWindowStyles);
    }
    return (
        <div className="d-flex justify-content-center align-items-center" style={props.fixed ? alertWindowStyles : ''}>
            <div className={'alert alert-' + props.class} role="alert">
                {props.message}<a href={props.actionHref} onClick={() => {props.action}} className="alert-link">{props.actionText}</a>
            </div>
        </div>
    );
}

export default Alert;