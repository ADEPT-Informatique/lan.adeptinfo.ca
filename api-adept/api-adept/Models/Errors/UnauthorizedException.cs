using System.Net;

namespace api_adept.Models.Errors
{
    public class UnAuthorizedException : AdeptException
    {
        public UnAuthorizedException(string pEntityName, string pMessage) : base("ERR_UNAUTHORIZED", pMessage, HttpStatusCode.BadRequest)
        {
            base.ErrorCode = $"{base.ErrorCode}_{pEntityName}";
        }
    }
}
