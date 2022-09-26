using api_adept.Context;
using api_adept.Models;
using Microsoft.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore.ChangeTracking;

namespace api_adept.Services
{
    public class LanService : AdeptService, ILanService
    {
        public LanService(AdeptLanContext adeptLanContext) : base(adeptLanContext)
        {
        }

        public Lan GetLatestLan()
        {
            return _context.Lans.Where(l => l.Date >= DateTime.Now).OrderByDescending(l => l.Date).FirstOrDefault();
        }

        public string GetSessionFromDate(DateTime date)
        {
            String currentYear = date.ToString("yy");
            if (date.Month >= 9 && date.Month <= 12)
            {
                return "A" + currentYear;
            } else if (date.Month >= 2 && date.Month <= 5)
            {
                return "H" + currentYear;
            }

            throw new ArgumentException("Date is not in a valid session range");
        }

        public Lan Create(Lan lan)
        {
            EntityEntry<Lan> insertedLan = _context.Lans.Add(lan);

            this.SaveChanges();

            return insertedLan.Entity;
        }
    }
}
