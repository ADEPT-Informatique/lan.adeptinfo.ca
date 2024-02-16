using api_adept.Models;

namespace api_adept.Services
{
    public interface ILanService
    {
        public string GetSessionFromDate(DateTime date);
        public Lan GetLatestLan();
        public Lan Create(Lan lan);
    }
}
