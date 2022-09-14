using System.Net;

namespace api_adept.Models.Errors
{
    public class AdeptException : Exception
    {
        protected AdeptException(string errorCode, string message, HttpStatusCode? httpStatus) : base(message)
        {
            _ = string.IsNullOrWhiteSpace(errorCode) ? throw new ArgumentNullException(nameof(errorCode), $"{nameof(AdeptException)} was expecting a value for {nameof(errorCode)} but null or empty was provided") : string.Empty;

            this.ErrorCode = errorCode;
            this.HttpStatus = httpStatus;
        }

        public string ErrorCode { get; set; }
        public HttpStatusCode? HttpStatus { get; set; }
    }
}
