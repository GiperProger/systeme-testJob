import FormatChecker from './FormatChecker.js';

export default class RequestSender
{
    static async sendPostRequest(url, postData)
    {
        if(!FormatChecker.isJsonString(postData)){
          return false;
        }

        return await fetch(url, {
          method: "POST",
          body: postData,
          headers: {
            "Content-type": "application/json; charset=UTF-8"
          }
        });
    }
}