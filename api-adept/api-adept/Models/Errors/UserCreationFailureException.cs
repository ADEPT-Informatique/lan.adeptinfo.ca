using System.Net;

namespace api_adept.Models.Errors
{
    public class UserCreationFailureException : AdeptException
    {
        public UserCreationFailureException(string entityName, string message) : base("ERR_USERCREATIONFAILURE", message, HttpStatusCode.BadRequest)
        {
            base.ErrorCode = $"{base.ErrorCode}_{entityName}";
        }
    }
}
