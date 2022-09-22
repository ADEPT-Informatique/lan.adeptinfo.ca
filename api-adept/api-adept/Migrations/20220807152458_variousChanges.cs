using System;
using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace api_adept.Migrations
{
    public partial class variousChanges : Migration
    {
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropForeignKey(
                name: "FK_Seats_Lans_LanId",
                table: "Seats");

            migrationBuilder.DropIndex(
                name: "IX_Participants_Email",
                table: "Participants");

            migrationBuilder.AlterColumn<long>(
                name: "LanId",
                table: "Seats",
                type: "bigint",
                nullable: true,
                oldClrType: typeof(long),
                oldType: "bigint");

            migrationBuilder.AddColumn<int>(
                name: "Number",
                table: "Seats",
                type: "int",
                nullable: false,
                defaultValue: 0);

            migrationBuilder.AddColumn<string>(
                name: "Section",
                table: "Seats",
                type: "nvarchar(1)",
                nullable: false,
                defaultValue: "");

            migrationBuilder.AlterColumn<string>(
                name: "LastName",
                table: "Participants",
                type: "nvarchar(max)",
                nullable: true,
                oldClrType: typeof(string),
                oldType: "nvarchar(max)");

            migrationBuilder.AlterColumn<string>(
                name: "FirstName",
                table: "Participants",
                type: "nvarchar(max)",
                nullable: true,
                oldClrType: typeof(string),
                oldType: "nvarchar(max)");

            migrationBuilder.AlterColumn<string>(
                name: "Email",
                table: "Participants",
                type: "nvarchar(450)",
                nullable: true,
                oldClrType: typeof(string),
                oldType: "nvarchar(450)");

            migrationBuilder.AddColumn<long>(
                name: "ReservationId",
                table: "Participants",
                type: "bigint",
                nullable: true);

            migrationBuilder.AlterColumn<string>(
                name: "Session",
                table: "Lans",
                type: "nvarchar(max)",
                nullable: true,
                oldClrType: typeof(string),
                oldType: "nvarchar(max)");

            migrationBuilder.CreateTable(
                name: "Reservations",
                columns: table => new
                {
                    Id = table.Column<long>(type: "bigint", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    Date = table.Column<DateTime>(type: "datetime2", nullable: false),
                    LanId = table.Column<long>(type: "bigint", nullable: true),
                    SeatId = table.Column<long>(type: "bigint", nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_Reservations", x => x.Id);
                    table.ForeignKey(
                        name: "FK_Reservations_Lans_LanId",
                        column: x => x.LanId,
                        principalTable: "Lans",
                        principalColumn: "Id");
                    table.ForeignKey(
                        name: "FK_Reservations_Seats_SeatId",
                        column: x => x.SeatId,
                        principalTable: "Seats",
                        principalColumn: "Id",
                        onDelete: ReferentialAction.Cascade);
                });

            migrationBuilder.CreateIndex(
                name: "IX_Participants_Email",
                table: "Participants",
                column: "Email",
                unique: true,
                filter: "[Email] IS NOT NULL");

            migrationBuilder.CreateIndex(
                name: "IX_Participants_ReservationId",
                table: "Participants",
                column: "ReservationId");

            migrationBuilder.CreateIndex(
                name: "IX_Reservations_LanId",
                table: "Reservations",
                column: "LanId");

            migrationBuilder.CreateIndex(
                name: "IX_Reservations_SeatId",
                table: "Reservations",
                column: "SeatId",
                unique: true);

            migrationBuilder.AddForeignKey(
                name: "FK_Participants_Reservations_ReservationId",
                table: "Participants",
                column: "ReservationId",
                principalTable: "Reservations",
                principalColumn: "Id");

            migrationBuilder.AddForeignKey(
                name: "FK_Seats_Lans_LanId",
                table: "Seats",
                column: "LanId",
                principalTable: "Lans",
                principalColumn: "Id");
        }

        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropForeignKey(
                name: "FK_Participants_Reservations_ReservationId",
                table: "Participants");

            migrationBuilder.DropForeignKey(
                name: "FK_Seats_Lans_LanId",
                table: "Seats");

            migrationBuilder.DropTable(
                name: "Reservations");

            migrationBuilder.DropIndex(
                name: "IX_Participants_Email",
                table: "Participants");

            migrationBuilder.DropIndex(
                name: "IX_Participants_ReservationId",
                table: "Participants");

            migrationBuilder.DropColumn(
                name: "Number",
                table: "Seats");

            migrationBuilder.DropColumn(
                name: "Section",
                table: "Seats");

            migrationBuilder.DropColumn(
                name: "ReservationId",
                table: "Participants");

            migrationBuilder.AlterColumn<long>(
                name: "LanId",
                table: "Seats",
                type: "bigint",
                nullable: false,
                defaultValue: 0L,
                oldClrType: typeof(long),
                oldType: "bigint",
                oldNullable: true);

            migrationBuilder.AlterColumn<string>(
                name: "LastName",
                table: "Participants",
                type: "nvarchar(max)",
                nullable: false,
                defaultValue: "",
                oldClrType: typeof(string),
                oldType: "nvarchar(max)",
                oldNullable: true);

            migrationBuilder.AlterColumn<string>(
                name: "FirstName",
                table: "Participants",
                type: "nvarchar(max)",
                nullable: false,
                defaultValue: "",
                oldClrType: typeof(string),
                oldType: "nvarchar(max)",
                oldNullable: true);

            migrationBuilder.AlterColumn<string>(
                name: "Email",
                table: "Participants",
                type: "nvarchar(450)",
                nullable: false,
                defaultValue: "",
                oldClrType: typeof(string),
                oldType: "nvarchar(450)",
                oldNullable: true);

            migrationBuilder.AlterColumn<string>(
                name: "Session",
                table: "Lans",
                type: "nvarchar(max)",
                nullable: false,
                defaultValue: "",
                oldClrType: typeof(string),
                oldType: "nvarchar(max)",
                oldNullable: true);

            migrationBuilder.CreateIndex(
                name: "IX_Participants_Email",
                table: "Participants",
                column: "Email",
                unique: true);

            migrationBuilder.AddForeignKey(
                name: "FK_Seats_Lans_LanId",
                table: "Seats",
                column: "LanId",
                principalTable: "Lans",
                principalColumn: "Id",
                onDelete: ReferentialAction.Cascade);
        }
    }
}
