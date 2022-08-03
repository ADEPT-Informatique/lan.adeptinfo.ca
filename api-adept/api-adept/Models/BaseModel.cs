using System.ComponentModel.DataAnnotations;

namespace api_adept.Models
{
    public abstract class BaseModel
    {
        [Key]
        public long Id { get; set; }
    }
}
