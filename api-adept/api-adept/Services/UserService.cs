namespace api_adept.Services
{
    /// <summary>  
    /// Offers services for user specific operations  
    /// </summary>  
    public class UserServices : IUserService
    {

        /// <summary>  
        /// Public constructor.  
        /// </summary>  
        public UserServices()
        {
        }

        /// <summary>  
        /// Public method to authenticate user by user name and word.  
        /// </summary>  
        /// <param name="userName"></param>  
        /// <param name="word"></param>  
        /// <returns></returns>  
        public int Authenticate(string token)
        {

            return 0;
        }
    }
}