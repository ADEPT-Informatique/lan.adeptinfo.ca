namespace api_adept.Models.Errors
{
    public class Error
    {
        public string ErrorCode { get; set; }

        public string Message { get; set; }

        public string Stacktrace { get; set; }
    }
}
