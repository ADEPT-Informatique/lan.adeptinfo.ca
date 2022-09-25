using System;
using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace api_adept.Migrations
{
    public partial class LansUpdate : Migration
    {
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.RenameColumn(
                name: "Date",
                table: "Lans",
                newName: "StartingDate");

            migrationBuilder.AddColumn<DateTime>(
                name: "InscriptionDate",
                table: "Lans",
                type: "datetime2",
                nullable: false,
                defaultValue: new DateTime(1, 1, 1, 0, 0, 0, 0, DateTimeKind.Unspecified));
        }

        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropColumn(
                name: "InscriptionDate",
                table: "Lans");

            migrationBuilder.RenameColumn(
                name: "StartingDate",
                table: "Lans",
                newName: "Date");
        }
    }
}
