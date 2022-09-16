using System.Net;

namespace api_adept.Models.Errors
{
    public class NotFoundException : AdeptException
    {
        public NotFoundException(string entityName, string message) : base("ERR_NOTFOUND", message, HttpStatusCode.NotFound) 
        {
            base.ErrorCode = $"{base.ErrorCode}_{entityName}";
        }
    }
}
