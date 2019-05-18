export default interface ISubIdxLanguage {
    id: number,
    index: string,
    language: string,
    hasError: boolean,
    canBeRequested: boolean,
    isQueued: boolean,
    queuePosition: number | null,
    isProcessing: boolean,
    downloadUrl: string | false,
}
