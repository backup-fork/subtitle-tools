import {AxiosInstance} from "axios";
import {Promise} from "es6-promise";
import ISubIdx from "../interfaces/sub-idx";

declare const axios: AxiosInstance;

export const getSubIdxBatchResults = (subIdxBatchId: string) => {
    return new Promise<ISubIdx[]>((resolve, reject) => {
        axios.get(`/api/v1/sub-idx-batch/${subIdxBatchId}/result`).then((response: any) => resolve(response.data.data));
    });
};
