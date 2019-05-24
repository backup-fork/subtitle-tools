import React from 'react';

declare const window: { Laravel: { csrf_token: string }};

export const DownloadLink = (props: {url: string, text: string}) => (
    <form action={props.url} method="post" encType="multipart/form-data" target="_blank">
        <input type="hidden" name="_token" value={window.Laravel.csrf_token}/>

        <button className="cursor-pointer">
            {props.text}
            <svg className="ml-2 h-4 w-4">
                <use xlinkHref="#download-arrow"/>
            </svg>
        </button>
    </form>
);
