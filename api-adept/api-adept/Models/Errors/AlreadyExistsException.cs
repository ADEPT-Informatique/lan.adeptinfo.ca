using System.Net;

namespace api_adept.Models.Errors
{
    public class AlreadyExistsException : AdeptException
    {
        public AlreadyExistsException(string pEntityName, string pMessage) : base("ERR_EXIST", pMessage, HttpStatusCode.Conflict) 
        {
            base.ErrorCode = $"{base.ErrorCode}_{pEntityName}";
        }
    }
}
