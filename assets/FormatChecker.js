
export default class FormatChecker
{
  static isJsonString(str) 
  {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
  }
}