using System.Net;

namespace api_adept.Models.Errors
{
    public class AlreadyAppliedException : AdeptException
    {
        public AlreadyAppliedException(string pEntityName, string pMessage) : base("ERR_ALREADYAPPLIED", pMessage, HttpStatusCode.BadRequest)
        {
            ErrorCode = $"{ErrorCode}_{pEntityName}";
        }
    }
}
