using api_adept.Context;

namespace api_adept.Services
{
    public class AdeptService
    {
        protected AdeptLanContext _context;

        public AdeptService(AdeptLanContext context)
        {
            _context = context;
        }

        protected void SaveChanges()
        {
            _context.SaveChanges();
        }
    }
}
