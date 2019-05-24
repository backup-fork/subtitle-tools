import React, {useEffect, useState} from 'react';
import ReactDOM from 'react-dom';
import {getSubIdxBatchResults} from "./services/api";
import ISubIdx from "./interfaces/sub-idx";
import {DownloadLink} from "./download-link";

const SubIdxBatchResult = (props: {subIdxBatchId: string}) => {
    const [data, setData] = useState<ISubIdx[]|null>(null);

    useEffect(() => {
        getSubIdxBatchResults(props.subIdxBatchId).then(setData);

        setInterval(() => {
            getSubIdxBatchResults(props.subIdxBatchId).then(setData);
        }, 5000);
    }, []);

    if (! data) {
        return <div>Loading...</div>;
    }

    return (
        <>
            {data.map(subIdx => (
                <div className="px-2 mb-8" key={subIdx.id}>
                    <div className="flex">
                        <strong className="flex-grow">{subIdx.originalName}</strong>

                        {subIdx.languages.length > 0 && (
                            <div className="w-48 text-right mb-4">
                                {subIdx.languages.every(i => !!i.downloadUrl) ? (
                                    <DownloadLink url={subIdx.downloadZipUrl} text="Download all as zip"/>
                                ) : (
                                    <div className="text-grey cursor-not-allowed">Download all as zip</div>
                                )}
                            </div>
                        )}
                    </div>

                    {subIdx.languages.length === 0 && (
                        <div className="py-1">This sub/idx doesn't contain any of the languages you have selected.</div>
                    )}

                    {subIdx.languages.map(language => (
                        <div className="flex py-1 ml-4 mb-2 border-b hover:bg-grey-lightest" key={language.id}>
                            <div className="flex-grow">{language.language}</div>
                            <div className="w-32 text-right">
                                {language.isProcessing && 'Processing...'}
                                {language.isQueued && 'Queued...'}
                                {language.downloadUrl && <DownloadLink url={language.downloadUrl} text="Download"/>}
                            </div>
                        </div>
                    ))}
                </div>
            ))}
        </>
    );
};


document.querySelectorAll<HTMLElement>('#sub-idx-batch-result').forEach(el => {
    ReactDOM.render(<SubIdxBatchResult subIdxBatchId={el.dataset.batchId as string} />, el);
});
