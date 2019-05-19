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
        return <div/>;
    }

    return (
        <div className="max-w-md">
            {data.map(subIdx => (
                <div className="p-4 mb-6 bg-white border rounded" key={subIdx.id}>
                    <div className="flex mb-4">
                        <input type="text" className="flex-grow mr-6" value={subIdx.originalName} readOnly={true} />
                        <div className="w-48 text-right">
                            {subIdx.languages.every(i => !!i.downloadUrl) ? (
                                <DownloadLink url={subIdx.downloadZipUrl} text="Download all as zip"/>
                            ) : (
                                <div className="text-grey cursor-not-allowed">Download all as zip</div>
                            )}
                        </div>
                    </div>

                    {subIdx.languages.map(language => (
                        <div className="flex border-b py-2 ml-4 mb-2 hover:bg-grey-lightest" key={language.id}>
                            <div className="flex-grow">{language.language}</div>
                            <div className="w-32 text-right">
                                {language.isProcessing && 'Processing...'}
                                {language.isQueued && 'Queued...'}
                                {language.downloadUrl && <a href={language.downloadUrl}>Download</a>}
                            </div>
                        </div>
                    ))}
                </div>
            ))}
        </div>
    );
};


document.querySelectorAll<HTMLElement>('#sub-idx-batch-result').forEach(el => {
    ReactDOM.render(<SubIdxBatchResult subIdxBatchId={el.dataset.batchId as string} />, el);
});
