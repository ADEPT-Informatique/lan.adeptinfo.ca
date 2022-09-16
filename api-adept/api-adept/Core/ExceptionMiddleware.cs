using api_adept.Models.Errors;
using Newtonsoft.Json;
using System.Net;

namespace api_adept.Core
{
    public class ExceptionMiddleware
    {
        private readonly RequestDelegate _request;

        public ExceptionMiddleware(RequestDelegate request)
        {
            _request = request;
        }

        public async Task Invoke(HttpContext context)
        {
            try
            {
                await _request(context);
            }
            catch (Exception exception)
            {
                var contextResponse = context.Response;
                contextResponse.ContentType = "application/json";

                var error = new Error();
                if (exception is AdeptException adeptException)
                {
                    contextResponse.StatusCode = (int)HttpStatusCode.BadRequest;
                    error = new Error { ErrorCode = adeptException.ErrorCode, Message = adeptException.Message };
                }
                else
                {
                    contextResponse.StatusCode = (int)HttpStatusCode.InternalServerError;
                    error = new Error { ErrorCode = "ERR_UNHANDLED", Message = "Unhandled Error", Stacktrace = "Unhandled Error" };
                }

                await contextResponse.WriteAsync(JsonConvert.SerializeObject(error));
            }
        }
    }
}
