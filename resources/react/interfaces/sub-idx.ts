import ISubIdxLanguage from "./sub-idx-language";

export default interface ISubIdx {
    id: number,
    originalName: string,
    languages: Array<ISubIdxLanguage>,
}
